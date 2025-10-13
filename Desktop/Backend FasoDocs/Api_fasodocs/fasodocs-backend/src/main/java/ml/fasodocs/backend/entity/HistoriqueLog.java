package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;

/**
 * Entité représentant l'historique des actions d'un utilisateur
 */
@Entity
@Table(name = "historique_logs")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class HistoriqueLog {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 100)
    private String typeAction; // CONSULTATION, RECHERCHE, MODIFICATION_PROFIL, etc.

    @Column(name = "date_action")
    @CreationTimestamp
    private LocalDateTime dateAction;

    @Column(columnDefinition = "TEXT")
    private String details;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "citoyen_id", nullable = false)
    private Citoyen citoyen;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "procedure_id")
    private Procedure procedure;
}
