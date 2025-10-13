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
 * Entité représentant un citoyen utilisateur de l'application FasoDocs
 */
@Entity
@Table(name = "citoyens")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Citoyen {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 100)
    private String nom;

    @Column(nullable = false, length = 100)
    private String prenom;

    @Column(length = 20)
    private String telephone;

    @Column(unique = true, length = 100)
    private String email;

    @Column(nullable = false)
    private String motDePasse;

    @Column(name = "est_actif")
    private Boolean estActif = true;

    @Column(name = "email_verifie")
    private Boolean emailVerifie = false;

    @Column(name = "code_verification")
    private String codeVerification;

    @Column(name = "token_fcm")
    private String tokenFcm; // Token Firebase Cloud Messaging pour les notifications push

    @Column(name = "langue_preferee", length = 2)
    private String languePreferee = "fr"; // fr ou bm (bambara)

    @CreationTimestamp
    @Column(name = "date_creation", updatable = false)
    private LocalDateTime dateCreation;

    @UpdateTimestamp
    @Column(name = "date_modification")
    private LocalDateTime dateModification;

    @OneToMany(mappedBy = "citoyen", cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<HistoriqueLog> historiques = new HashSet<>();

    @OneToMany(mappedBy = "citoyen", cascade = CascadeType.ALL, orphanRemoval = true)
    private Set<Notification> notifications = new HashSet<>();

    @ManyToMany(fetch = FetchType.EAGER)
    @JoinTable(
        name = "citoyen_roles",
        joinColumns = @JoinColumn(name = "citoyen_id"),
        inverseJoinColumns = @JoinColumn(name = "role_id")
    )
    private Set<Role> roles = new HashSet<>();

    /**
     * Méthode pour créer un compte
     */
    public void creerCompte() {
        this.estActif = true;
        this.emailVerifie = false;
        this.dateCreation = LocalDateTime.now();
    }

    /**
     * Méthode pour se connecter (validation sera faite dans le service)
     */
    public boolean seConnecter(String motDePasseSaisi) {
        return this.estActif && this.emailVerifie;
    }

    /**
     * Méthode pour consulter le profil
     */
    public Citoyen consulterProfil() {
        return this;
    }

    /**
     * Méthode pour modifier le profil
     */
    public void modifierProfil(String nom, String prenom, String telephone) {
        this.nom = nom;
        this.prenom = prenom;
        this.telephone = telephone;
    }

    /**
     * Méthode pour modifier les paramètres
     */
    public void modifierParametres(String languePreferee) {
        this.languePreferee = languePreferee;
    }
}
