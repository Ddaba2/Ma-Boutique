package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Entité représentant une notification envoyée à un citoyen
 */
@Entity
@Table(name = "notifications")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Notification {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 200)
    private String contenu;

    @Column(name = "date_envoi")
    @CreationTimestamp
    private LocalDateTime dateEnvoi;

    @Column(name = "est_lue")
    private Boolean estLue = false;

    @Column(nullable = false, length = 50)
    private String type; // INFO, MISE_A_JOUR, ALERTE

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "citoyen_id", nullable = false)
    private Citoyen citoyen;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "procedure_id")
    private Procedure procedure; // Procédure concernée si applicable

    /**
     * Marque la notification comme lue
     */
    public void marquerLue() {
        this.estLue = true;
    }
}
