package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;

import java.time.LocalDateTime;
import java.util.HashSet;
import java.util.Set;

/**
 * Entité représentant une catégorie de procédures administratives
 */
@Entity
@Table(name = "categories")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Categorie {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, unique = true, length = 100)
    private String titre;

    @Column(columnDefinition = "TEXT")
    private String description;

    @Column(name = "icone_url")
    private String iconeUrl;

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    @OneToMany(mappedBy = "categorie", cascade = CascadeType.ALL)
    private Set<Procedure> procedures = new HashSet<>();

    /**
     * Méthode pour consulter la catégorie
     */
    public Categorie consulterCategorie() {
        return this;
    }
}
