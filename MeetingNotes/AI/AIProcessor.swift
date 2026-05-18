import Foundation
import FoundationModels

@Observable
@MainActor
final class AIProcessor {
    enum State: Equatable {
        case idle
        case processing
        case done(StructuredMeetingNotes)
        case error(String)
    }

    var state: State = .idle

    private let instructions = """
        You are an expert assistant that turns raw, messy meeting notes into a
        clean, structured summary. Be faithful to the source — do not invent
        names, decisions or deadlines that are not present. Keep the output in
        the same language as the source notes. Be concise and professional.
        """

    func process(rawNotes: String) async {
        let model = SystemLanguageModel.default
        guard case .available = model.availability else {
            state = .error(Self.unavailabilityMessage(for: model.availability))
            return
        }

        state = .processing
        do {
            let session = LanguageModelSession(instructions: instructions)
            let response = try await session.respond(
                to: "Process these meeting notes:\n\n\(rawNotes)",
                generating: StructuredMeetingNotes.self
            )
            state = .done(response.content)
        } catch {
            state = .error(error.localizedDescription)
        }
    }

    private static func unavailabilityMessage(
        for availability: SystemLanguageModel.Availability
    ) -> String {
        switch availability {
        case .available:
            return ""
        case .unavailable(.deviceNotEligible):
            return "This device does not support Apple Intelligence."
        case .unavailable(.appleIntelligenceNotEnabled):
            return "Enable Apple Intelligence in Settings to use on-device AI."
        case .unavailable(.modelNotReady):
            return "The on-device model is still downloading. Try again shortly."
        case .unavailable(let other):
            return "On-device model unavailable: \(other)"
        }
    }
}
