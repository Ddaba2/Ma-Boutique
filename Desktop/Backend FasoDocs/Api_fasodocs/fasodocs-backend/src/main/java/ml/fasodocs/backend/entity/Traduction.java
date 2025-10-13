package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Entité représentant une traduction de contenu
 */
@Entity
@Table(name = "traductions")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Traduction {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 2)
    private String langue; // Code ISO 639-1 (fr, bm pour bambara)

    @Column(name = "contenu_traduit", columnDefinition = "TEXT")
    private String contenuTraduit;

    @Column(name = "audio_url", length = 500)
    private String audioUrl; // URL vers le fichier audio de la synthèse vocale

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "procedure_id")
    private Procedure procedure;

    /**
     * Vérifie si cette traduction est en bambara
     */
    public boolean estEnBambara() {
        return "bm".equalsIgnoreCase(this.langue);
    }
}
