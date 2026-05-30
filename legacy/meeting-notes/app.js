import { CreateMLCEngine } from "https://esm.run/@mlc-ai/web-llm";

const MODEL = "Llama-3.2-3B-Instruct-q4f16_1-MLC";

const SYSTEM_PROMPT = `You are an assistant that turns raw, messy meeting notes into a clean, structured JSON summary.

Rules:
- Keep the output in the SAME LANGUAGE as the input notes.
- Do not invent attendees, decisions, action items, deadlines or numbers that are not in the source. If something is missing, use "TBD" for strings or an empty array.
- Be concise and professional.
- Output ONLY a valid JSON object that matches the schema below — no prose, no markdown, no code fences.

Schema:
{
  "title": string,                                  // short descriptive meeting title
  "date": string,                                   // human readable, e.g. "18 May 2026" or "18. Mai 2026"
  "attendees": string[],                            // names only
  "summary": string,                                // 2-3 sentence executive summary
  "keyPoints": string[],                            // main discussion topics
  "decisions": string[],                            // decisions made
  "actionItems": [{ "task": string, "assignee": string, "deadline": string }]
}`;

const JSON_SCHEMA = {
  type: "object",
  properties: {
    title: { type: "string" },
    date: { type: "string" },
    attendees: { type: "array", items: { type: "string" } },
    summary: { type: "string" },
    keyPoints: { type: "array", items: { type: "string" } },
    decisions: { type: "array", items: { type: "string" } },
    actionItems: {
      type: "array",
      items: {
        type: "object",
        properties: {
          task: { type: "string" },
          assignee: { type: "string" },
          deadline: { type: "string" },
        },
        required: ["task", "assignee", "deadline"],
      },
    },
  },
  required: ["title", "date", "attendees", "summary", "keyPoints", "decisions", "actionItems"],
};

const SAMPLE = `Roadmap-Sync — 18. Mai 2026
Anwesend: Anna, Ben, Cleo, Dev

- Q3-Prioritäten diskutiert. Großer Push auf on-device AI-Features.
- Anna schlägt vor, PDF-Export der Meeting-Zusammenfassungen zu shippen. Cleo stimmt zu.
- Ben äußert Bedenken zu Devices ohne Apple Intelligence. Dev klärt Fallback bis 31. Mai.
- Entscheidung: Foundation-Models-Framework als Standard für Summaries adoptieren.
- Entscheidung: PDF-Export im nächsten Minor-Release shippen.
- Action: Ben schreibt Integrations-Spec bis 24. Mai.
- Action: Cleo entwirft PDF-Template-Varianten bis 27. Mai.`;

// ===== DOM helpers =====
const $ = (id) => document.getElementById(id);
const rawNotesEl = $("rawNotes");
const processBtn = $("processBtn");
const printBtn = $("printBtn");
const sampleBtn = $("sampleBtn");
const processLabel = $("processLabel");
const modelStatus = $("modelStatus");
const modelDot = $("modelDot");
const modelStatusLabel = $("modelStatusLabel");
const emptyState = $("emptyState");
const sheetContent = $("sheetContent");
const toastEl = $("toast");

// ===== State =====
let engine = null;
let engineReady = false;
let toastTimer = null;

function setModelStatus(state, label) {
  modelStatus.dataset.state = state;
  modelDot.className = `dot ${state}`;
  modelStatusLabel.textContent = label;
}

function showToast(msg) {
  toastEl.textContent = msg;
  toastEl.hidden = false;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { toastEl.hidden = true; }, 3000);
}

function updateProcessButton() {
  processBtn.disabled = !engineReady || !rawNotesEl.value.trim();
}

// ===== Engine =====
async function loadEngine() {
  if (!("gpu" in navigator)) {
    setModelStatus("error", "WebGPU nicht verfügbar");
    showToast("Dieser Browser unterstützt kein WebGPU. Nutze Chrome oder Edge.");
    return;
  }

  setModelStatus("loading", "Initialisiere …");
  try {
    engine = await CreateMLCEngine(MODEL, {
      initProgressCallback: (report) => {
        const pct = Math.round((report.progress ?? 0) * 100);
        const text = report.text ?? "";
        if (text) {
          // Compact label, keep percentage if present
          const compact = text.length > 38 ? text.slice(0, 36) + "…" : text;
          setModelStatus("loading", pct > 0 ? `${compact} · ${pct}%` : compact);
        } else {
          setModelStatus("loading", `Lade … ${pct}%`);
        }
      },
    });
    engineReady = true;
    setModelStatus("ready", "Llama 3.2 · bereit");
    updateProcessButton();
  } catch (err) {
    console.error(err);
    setModelStatus("error", "Fehler beim Laden");
    showToast("Modell konnte nicht geladen werden: " + (err?.message || err));
  }
}

// ===== Processing =====
async function processNotes(notes) {
  if (!engineReady) {
    showToast("Modell noch nicht bereit");
    return;
  }

  processBtn.disabled = true;
  processLabel.textContent = "Verarbeite …";
  printBtn.disabled = true;

  try {
    const completion = await engine.chat.completions.create({
      messages: [
        { role: "system", content: SYSTEM_PROMPT },
        { role: "user", content: `Verarbeite diese Meeting-Notizen und antworte ausschließlich mit gültigem JSON gemäß Schema:\n\n${notes}` },
      ],
      temperature: 0.2,
      max_tokens: 1500,
      response_format: {
        type: "json_object",
        schema: JSON.stringify(JSON_SCHEMA),
      },
    });

    const raw = completion.choices[0].message.content.trim();
    const data = parseJsonLoosely(raw);
    renderSheet(data);
    printBtn.disabled = false;
    showToast("Notizen aufbereitet ✓");
  } catch (err) {
    console.error(err);
    showToast("Fehler: " + (err?.message || err));
  } finally {
    processLabel.textContent = "Mit lokaler KI verarbeiten";
    updateProcessButton();
  }
}

function parseJsonLoosely(text) {
  try {
    return JSON.parse(text);
  } catch {
    // Strip code fences or surrounding prose if the model added them anyway
    const match = text.match(/\{[\s\S]*\}/);
    if (match) return JSON.parse(match[0]);
    throw new Error("Antwort war kein gültiges JSON");
  }
}

// ===== Rendering =====
function renderSheet(data) {
  $("outTitle").textContent = data.title || "Meeting";
  $("outDate").textContent = data.date || "—";

  const attendees = Array.isArray(data.attendees) ? data.attendees : [];
  $("outAttendees").textContent = attendees.length ? attendees.join("  ·  ") : "—";
  $("outAttendeeCount").textContent = attendees.length;
  toggleSection("sectionAttendees", attendees.length > 0);

  $("outSummary").textContent = data.summary || "—";
  toggleSection("sectionSummary", !!data.summary);

  renderList("outKeyPoints", data.keyPoints, "sectionKeyPoints");
  renderList("outDecisions", data.decisions, "sectionDecisions");
  renderActions("outActions", data.actionItems, "sectionActions");

  $("outFooterDate").textContent =
    "Erstellt am " +
    new Date().toLocaleDateString("de-CH", { day: "numeric", month: "short", year: "numeric" });

  emptyState.hidden = true;
  sheetContent.hidden = false;
}

function toggleSection(sectionId, show) {
  const el = $(sectionId);
  if (!el) return;
  el.hidden = !show;
}

function renderList(listId, items, sectionId) {
  const ul = $(listId);
  ul.innerHTML = "";
  const arr = Array.isArray(items) ? items.filter((s) => s && String(s).trim()) : [];
  if (arr.length === 0) {
    toggleSection(sectionId, false);
    return;
  }
  toggleSection(sectionId, true);
  for (const item of arr) {
    const li = document.createElement("li");
    li.textContent = String(item);
    ul.appendChild(li);
  }
}

function renderActions(containerId, items, sectionId) {
  const container = $(containerId);
  container.innerHTML = "";
  const arr = Array.isArray(items) ? items.filter((i) => i && i.task) : [];
  if (arr.length === 0) {
    toggleSection(sectionId, false);
    return;
  }
  toggleSection(sectionId, true);
  for (const item of arr) {
    const row = document.createElement("div");
    row.className = "action-item";

    const wrap = document.createElement("div");
    wrap.className = "action-task-wrap";

    const task = document.createElement("div");
    task.className = "action-task";
    task.textContent = item.task;

    const assignee = document.createElement("div");
    assignee.className = "action-assignee";
    assignee.textContent = item.assignee || "Unzugewiesen";

    wrap.append(task, assignee);

    const pill = document.createElement("span");
    pill.className = "action-pill";
    pill.textContent = item.deadline || "TBD";

    row.append(wrap, pill);
    container.appendChild(row);
  }
}

// ===== Wiring =====
rawNotesEl.addEventListener("input", updateProcessButton);
processBtn.addEventListener("click", () => processNotes(rawNotesEl.value));
sampleBtn.addEventListener("click", () => {
  rawNotesEl.value = SAMPLE;
  updateProcessButton();
});
printBtn.addEventListener("click", () => window.print());

// Boot
loadEngine();
