package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

/**
 * Entité représentant un document requis pour une procédure
 */
@Entity
@Table(name = "documents_requis")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class DocumentRequis {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 200)
    private String description;

    @Column(name = "est_obligatoire")
    private Boolean estObligatoire = true;

    @Column(name = "modele_url", length = 500)
    private String modeleUrl; // URL vers un modèle de document si disponible

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "procedure_id", nullable = false)
    private Procedure procedure;

    /**
     * Méthode pour consulter le document
     */
    public DocumentRequis consulterDocument() {
        return this;
    }
}
