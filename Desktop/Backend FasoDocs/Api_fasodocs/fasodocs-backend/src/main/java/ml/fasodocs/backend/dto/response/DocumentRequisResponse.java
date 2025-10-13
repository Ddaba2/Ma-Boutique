package ml.fasodocs.backend.dto.response;

import lombok.Data;

/**
 * DTO pour la réponse d'un document requis
 */
@Data
public class DocumentRequisResponse {
    private Long id;
    private String description;
    private Boolean estObligatoire;
    private String modeleUrl;
}
