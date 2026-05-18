import SwiftUI
import CoreGraphics

@MainActor
enum PDFGenerator {
    static func generate(from notes: StructuredMeetingNotes) -> URL? {
        let renderer = ImageRenderer(content: PDFTemplate(notes: notes))
        renderer.proposedSize = .init(PDFTemplate.pageSize)

        let filename = "MeetingNotes-\(Int(Date.now.timeIntervalSince1970)).pdf"
        let url = FileManager.default.temporaryDirectory.appending(path: filename)

        var didRender = false
        renderer.render { size, drawContent in
            var mediaBox = CGRect(origin: .zero, size: size)
            guard let context = CGContext(url as CFURL, mediaBox: &mediaBox, nil) else { return }
            context.beginPDFPage(nil)
            drawContent(context)
            context.endPDFPage()
            context.closePDF()
            didRender = true
        }

        return didRender ? url : nil
    }
}
