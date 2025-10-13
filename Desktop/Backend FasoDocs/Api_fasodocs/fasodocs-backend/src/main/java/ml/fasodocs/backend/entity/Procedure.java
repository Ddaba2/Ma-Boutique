package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;

import java.time.LocalDateTime;
import java.util.HashSet;
import java.util.Set;

/**
 * Entité représentant une procédure administrative
 */
@Entity
@Table(name = "procedures")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Procedure {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 200)
    private String nom;

    @Column(nullable = false, length = 100)
    private String titre;

    @Column(name = "url_vers_formulaire", length = 500)
    private String urlVersFormulaire;

    @Column(nullable = false)
    private Integer cout;

    @Column(nullable = false, length = 100)
    private String delai;

    @Column(columnDefinition = "TEXT")
    private String description;

    @ElementCollection
    @CollectionTable(name = "procedure_etapes", joinColumns = @JoinColumn(name = "procedure_id"))
    @Column(name = "etape", columnDefinition = "TEXT")
    private Set<String> etapes = new HashSet<>();

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    @UpdateTimestamp
    @Column(name = "date_modification")
    private LocalDateTime dateModification;

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "categorie_id", nullable = false)
    private Categorie categorie;

    @OneToMany(mappedBy = "procedure", cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<DocumentRequis> documentsRequis = new HashSet<>();

    @ManyToMany
    @JoinTable(
        name = "procedure_lieux",
        joinColumns = @JoinColumn(name = "procedure_id"),
        inverseJoinColumns = @JoinColumn(name = "lieu_id")
    )
    private Set<LieuDeTraitement> lieux = new HashSet<>();

    @OneToMany(mappedBy = "procedure", cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<Traduction> traductions = new HashSet<>();

    @OneToMany(mappedBy = "procedure", cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<LoiArticle> loisArticles = new HashSet<>();

    /**
     * Méthode pour consulter la procédure
     */
    public Procedure consulter() {
        return this;
    }

    /**
     * Méthode pour obtenir les étapes
     */
    public Set<String> afficherEtapes() {
        return this.etapes;
    }

    /**
     * Méthode pour afficher les documents requis
     */
    public Set<DocumentRequis> afficherDocumentsRequis() {
        return this.documentsRequis;
    }

    /**
     * Vérifie si le contenu doit être traduit
     */
    public boolean estATraduire() {
        return this.traductions.isEmpty();
    }
}
