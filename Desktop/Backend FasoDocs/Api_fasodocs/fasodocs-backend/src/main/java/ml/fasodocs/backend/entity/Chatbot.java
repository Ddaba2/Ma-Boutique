package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Entité représentant un chatbot pour l'assistance vocale
 */
@Entity
@Table(name = "chatbots")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Chatbot {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 100)
    private String type; // GUIDE_VOCAL, ASSISTANT_PROCEDURES, etc.

    @Column(columnDefinition = "TEXT")
    private String reponse;

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    /**
     * Analyse une question posée par l'utilisateur
     */
    public String analyserQuestion(String question) {
        // Logique d'analyse sera implémentée dans le service
        return this.reponse;
    }

    /**
     * Génère une réponse vocalisée
     */
    public String vocaliserReponse() {
        // Logique de vocalisation sera implémentée dans le service
        return "URL_AUDIO_REPONSE";
    }
}
