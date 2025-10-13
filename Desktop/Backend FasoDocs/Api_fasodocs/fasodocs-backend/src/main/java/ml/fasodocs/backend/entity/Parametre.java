package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.hibernate.annotations.CreationTimestamp;
import org.hibernate.annotations.UpdateTimestamp;

import java.time.LocalDateTime;

/**
 * Entité représentant un paramètre de l'application
 */
@Entity
@Table(name = "parametres")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Parametre {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, unique = true, length = 100)
    private String contenu;

    @Column(columnDefinition = "TEXT")
    private String valeur;

    @Column(length = 500)
    private String description;

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    @UpdateTimestamp
    @Column(name = "date_modification")
    private LocalDateTime dateModification;

    /**
     * Méthode pour consulter le paramètre
     */
    public Parametre consulter() {
        return this;
    }

    /**
     * Méthode pour modifier le paramètre
     */
    public void modifier(String nouvelleValeur) {
        this.valeur = nouvelleValeur;
    }
}
