import Foundation
import FoundationModels

@Generable
struct StructuredMeetingNotes: Equatable {
    @Guide(description: "A concise, descriptive title for the meeting in the same language as the source notes")
    let title: String

    @Guide(description: "Meeting date in human-readable form, e.g. '18 May 2026'. Use today if unknown.")
    let date: String

    @Guide(description: "List of attendee names extracted from the notes")
    let attendees: [String]

    @Guide(description: "Two to three sentence executive summary of the meeting")
    let summary: String

    @Guide(description: "Concise bullet points of the main discussion topics")
    let keyPoints: [String]

    @Guide(description: "Decisions that were explicitly made during the meeting")
    let decisions: [String]

    @Guide(description: "Concrete action items with owner and deadline")
    let actionItems: [ActionItem]
}

@Generable
struct ActionItem: Equatable, Identifiable {
    var id: String { task }

    @Guide(description: "Short description of the task to be done")
    let task: String

    @Guide(description: "Name of the person responsible. Use 'Unassigned' if unclear.")
    let assignee: String

    @Guide(description: "Deadline in human-readable form. Use 'TBD' if unspecified.")
    let deadline: String
}
