package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.util.HashSet;
import java.util.Set;

/**
 * Entité représentant un lieu de traitement des procédures administratives
 */
@Entity
@Table(name = "lieux_traitement")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class LieuDeTraitement {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 200)
    private String nom;

    @Column(nullable = false, length = 500)
    private String adresse;

    @Column(length = 100)
    private String horaires;

    @Column(name = "coordonnees_gps", length = 100)
    private String coordonneesGPS; // Format: "latitude,longitude"

    @Column(length = 20)
    private String telephone;

    @Column(length = 100)
    private String email;

    @ManyToMany(mappedBy = "lieux")
    private Set<Procedure> procedures = new HashSet<>();

    /**
     * Obtient la latitude à partir des coordonnées GPS
     */
    public Double getLatitude() {
        if (coordonneesGPS != null && coordonneesGPS.contains(",")) {
            String[] coords = coordonneesGPS.split(",");
            return Double.parseDouble(coords[0].trim());
        }
        return null;
    }

    /**
     * Obtient la longitude à partir des coordonnées GPS
     */
    public Double getLongitude() {
        if (coordonneesGPS != null && coordonneesGPS.contains(",")) {
            String[] coords = coordonneesGPS.split(",");
            return Double.parseDouble(coords[1].trim());
        }
        return null;
    }

    /**
     * Définit les coordonnées GPS
     */
    public void setCoordonnees(Double latitude, Double longitude) {
        this.coordonneesGPS = latitude + "," + longitude;
    }
}
