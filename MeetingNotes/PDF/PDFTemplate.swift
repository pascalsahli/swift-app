import SwiftUI

struct PDFTemplate: View {
    let notes: StructuredMeetingNotes

    static let pageSize = CGSize(width: 595, height: 842) // A4 @ 72 dpi
    private let accent = Color(red: 0.20, green: 0.30, blue: 0.85)

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            header
            Divider().padding(.vertical, 20)

            content

            Spacer(minLength: 0)
            footer
        }
        .padding(48)
        .frame(width: Self.pageSize.width, height: Self.pageSize.height, alignment: .topLeading)
        .background(Color.white)
        .foregroundStyle(Color.black)
    }

    private var header: some View {
        VStack(alignment: .leading, spacing: 10) {
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 4)
                    .fill(accent)
                    .frame(width: 4, height: 18)
                Text("MEETING NOTES")
                    .font(.system(size: 10, weight: .semibold))
                    .tracking(4)
                    .foregroundStyle(accent)
            }

            Text(notes.title)
                .font(.system(size: 28, weight: .bold))
                .lineLimit(2)

            HStack(spacing: 18) {
                Label(notes.date, systemImage: "calendar")
                Label("\(notes.attendees.count) attendees", systemImage: "person.2")
            }
            .font(.system(size: 11))
            .foregroundStyle(.secondary)
        }
    }

    private var content: some View {
        VStack(alignment: .leading, spacing: 22) {
            section("Attendees") {
                Text(notes.attendees.joined(separator: " · "))
                    .font(.system(size: 12))
            }

            section("Summary") {
                Text(notes.summary)
                    .font(.system(size: 12))
                    .lineSpacing(3)
            }

            if !notes.keyPoints.isEmpty {
                section("Key Discussion Points") {
                    VStack(alignment: .leading, spacing: 6) {
                        ForEach(notes.keyPoints, id: \.self) { point in
                            bullet(point)
                        }
                    }
                }
            }

            if !notes.decisions.isEmpty {
                section("Decisions") {
                    VStack(alignment: .leading, spacing: 6) {
                        ForEach(notes.decisions, id: \.self) { decision in
                            HStack(alignment: .top, spacing: 8) {
                                Image(systemName: "checkmark.circle.fill")
                                    .foregroundStyle(.green)
                                    .font(.system(size: 11))
                                    .padding(.top, 1)
                                Text(decision).font(.system(size: 12))
                            }
                        }
                    }
                }
            }

            if !notes.actionItems.isEmpty {
                section("Action Items") {
                    VStack(spacing: 6) {
                        ForEach(notes.actionItems) { item in
                            ActionItemRow(item: item, accent: accent)
                        }
                    }
                }
            }
        }
    }

    private var footer: some View {
        HStack {
            Text("Generated on \(Date.now.formatted(date: .abbreviated, time: .shortened))")
            Spacer()
            Text("On-device AI · Apple Intelligence")
        }
        .font(.system(size: 9))
        .foregroundStyle(.tertiary)
        .padding(.top, 16)
    }

    @ViewBuilder
    private func section<Content: View>(_ title: String, @ViewBuilder content: () -> Content) -> some View {
        VStack(alignment: .leading, spacing: 8) {
            Text(title.uppercased())
                .font(.system(size: 9, weight: .semibold))
                .tracking(2)
                .foregroundStyle(accent)
            content()
        }
    }

    private func bullet(_ text: String) -> some View {
        HStack(alignment: .top, spacing: 8) {
            Circle()
                .fill(Color.secondary)
                .frame(width: 3, height: 3)
                .padding(.top, 6)
            Text(text).font(.system(size: 12))
        }
    }
}

private struct ActionItemRow: View {
    let item: ActionItem
    let accent: Color

    var body: some View {
        HStack(alignment: .center, spacing: 12) {
            VStack(alignment: .leading, spacing: 2) {
                Text(item.task)
                    .font(.system(size: 12, weight: .medium))
                Text(item.assignee)
                    .font(.system(size: 10))
                    .foregroundStyle(.secondary)
            }
            Spacer(minLength: 8)
            Text(item.deadline)
                .font(.system(size: 10, weight: .semibold))
                .padding(.horizontal, 8)
                .padding(.vertical, 4)
                .background(accent.opacity(0.12), in: Capsule())
                .foregroundStyle(accent)
        }
        .padding(10)
        .background(Color(white: 0.97), in: RoundedRectangle(cornerRadius: 8))
    }
}

#Preview {
    PDFTemplate(notes: .sample)
}

extension StructuredMeetingNotes {
    static let sample = StructuredMeetingNotes(
        title: "Q3 Product Roadmap Review",
        date: "18 May 2026",
        attendees: ["Anna Müller", "Ben Tanner", "Cleo Rivera", "Dev Kapoor"],
        summary: "The team aligned on shipping the new on-device AI features in Q3, prioritising privacy-preserving meeting tooling and deferring the analytics rebuild to Q4.",
        keyPoints: [
            "On-device LLM lets us avoid network round trips for sensitive content.",
            "PDF export is the most requested feature from enterprise customers.",
            "Need to scope a fallback path for non-Apple-Intelligence devices."
        ],
        decisions: [
            "Adopt Foundation Models framework as the default for text summarisation.",
            "Ship PDF export in the next minor release."
        ],
        actionItems: [
            ActionItem(task: "Draft Foundation Models integration spec", assignee: "Ben", deadline: "24 May"),
            ActionItem(task: "Design PDF template variations", assignee: "Cleo", deadline: "27 May"),
            ActionItem(task: "Investigate fallback path for older devices", assignee: "Dev", deadline: "31 May")
        ]
    )
}
