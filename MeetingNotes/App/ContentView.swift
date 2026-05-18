import SwiftUI
import PDFKit

struct ContentView: View {
    @State private var rawNotes: String = ""
    @State private var processor = AIProcessor()
    @State private var pdfURL: URL?
    @State private var showingShare = false
    @State private var showingPreview = false

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                editor
                Divider()
                bottomBar
            }
            .navigationTitle("Meeting Notes")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .topBarTrailing) {
                    Menu {
                        Button("Insert sample notes") { rawNotes = .sampleNotes }
                        Button("Clear", role: .destructive) {
                            rawNotes = ""
                            processor.state = .idle
                            pdfURL = nil
                        }
                    } label: {
                        Image(systemName: "ellipsis.circle")
                    }
                }
            }
            .sheet(isPresented: $showingShare) {
                if let pdfURL { ShareSheet(items: [pdfURL]) }
            }
            .sheet(isPresented: $showingPreview) {
                if let pdfURL {
                    NavigationStack {
                        PDFPreview(url: pdfURL)
                            .navigationTitle("Preview")
                            .navigationBarTitleDisplayMode(.inline)
                            .toolbar {
                                ToolbarItem(placement: .topBarTrailing) {
                                    Button {
                                        showingShare = true
                                    } label: {
                                        Image(systemName: "square.and.arrow.up")
                                    }
                                }
                            }
                    }
                }
            }
        }
    }

    private var editor: some View {
        ZStack(alignment: .topLeading) {
            TextEditor(text: $rawNotes)
                .font(.system(size: 15))
                .padding(12)
                .scrollContentBackground(.hidden)

            if rawNotes.isEmpty {
                Text("Paste or write your raw meeting notes here…")
                    .foregroundStyle(.tertiary)
                    .padding(.horizontal, 18)
                    .padding(.vertical, 20)
                    .allowsHitTesting(false)
            }
        }
    }

    private var bottomBar: some View {
        HStack(spacing: 12) {
            statusView
            Spacer()

            if pdfURL != nil {
                Button {
                    showingPreview = true
                } label: {
                    Label("Preview PDF", systemImage: "doc.richtext")
                }
                .buttonStyle(.bordered)
            }

            Button {
                Task { await runProcessing() }
            } label: {
                Label("Process", systemImage: "sparkles")
                    .frame(minWidth: 80)
            }
            .buttonStyle(.borderedProminent)
            .disabled(rawNotes.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty || isProcessing)
        }
        .padding(12)
    }

    private var isProcessing: Bool {
        if case .processing = processor.state { return true }
        return false
    }

    @ViewBuilder
    private var statusView: some View {
        switch processor.state {
        case .idle:
            Label("On-device · private", systemImage: "lock.shield")
                .font(.caption)
                .foregroundStyle(.secondary)
        case .processing:
            HStack(spacing: 6) {
                ProgressView().controlSize(.small)
                Text("Processing on-device…").font(.caption)
            }
        case .done:
            Label("PDF ready", systemImage: "checkmark.circle.fill")
                .foregroundStyle(.green)
                .font(.caption)
        case .error(let message):
            Label(message, systemImage: "exclamationmark.triangle.fill")
                .foregroundStyle(.orange)
                .font(.caption)
                .lineLimit(2)
        }
    }

    private func runProcessing() async {
        pdfURL = nil
        await processor.process(rawNotes: rawNotes)
        if case .done(let notes) = processor.state {
            pdfURL = PDFGenerator.generate(from: notes)
        }
    }
}

private struct ShareSheet: UIViewControllerRepresentable {
    let items: [Any]

    func makeUIViewController(context: Context) -> UIActivityViewController {
        UIActivityViewController(activityItems: items, applicationActivities: nil)
    }

    func updateUIViewController(_ controller: UIActivityViewController, context: Context) {}
}

private struct PDFPreview: UIViewRepresentable {
    let url: URL

    func makeUIView(context: Context) -> PDFView {
        let view = PDFView()
        view.autoScales = true
        view.document = PDFDocument(url: url)
        return view
    }

    func updateUIView(_ view: PDFView, context: Context) {
        if view.document?.documentURL != url {
            view.document = PDFDocument(url: url)
        }
    }
}

private extension String {
    static let sampleNotes = """
        Roadmap sync — 18 May 2026
        Present: Anna, Ben, Cleo, Dev

        - Discussed Q3 priorities. Big push on on-device AI features.
        - Anna proposed shipping PDF export of meeting summaries. Cleo agreed.
        - Ben raised concern about devices that don't support Apple Intelligence.
          Dev to investigate fallback path by 31 May.
        - Decision: adopt Foundation Models framework for summarisation.
        - Decision: ship PDF export in next minor release.
        - Action: Ben drafts integration spec by 24 May.
        - Action: Cleo designs PDF template variants by 27 May.
        """
}

#Preview { ContentView() }
