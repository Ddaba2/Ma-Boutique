package ml.fasodocs.backend.dto.response;

import lombok.Data;

import java.time.LocalDateTime;

/**
 * DTO pour la réponse d'une notification
 */
@Data
public class NotificationResponse {
    private Long id;
    private String contenu;
    private LocalDateTime dateEnvoi;
    private Boolean estLue;
    private String type;
    private Long procedureId;
    private String procedureNom;
}
