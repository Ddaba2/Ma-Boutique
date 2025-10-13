package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.LieuDeTraitement;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

/**
 * Repository pour l'entité LieuDeTraitement
 */
@Repository
public interface LieuDeTraitementRepository extends JpaRepository<LieuDeTraitement, Long> {

    /**
     * Recherche des lieux par nom (contient)
     */
    @Query("SELECT l FROM LieuDeTraitement l WHERE LOWER(l.nom) LIKE LOWER(CONCAT('%', :nom, '%'))")
    List<LieuDeTraitement> rechercherParNom(@Param("nom") String nom);

    /**
     * Recherche des lieux par ville/adresse
     */
    @Query("SELECT l FROM LieuDeTraitement l WHERE LOWER(l.adresse) LIKE LOWER(CONCAT('%', :ville, '%'))")
    List<LieuDeTraitement> rechercherParVille(@Param("ville") String ville);

    /**
     * Recherche les lieux pour une procédure spécifique
     */
    @Query("SELECT l FROM LieuDeTraitement l JOIN l.procedures p WHERE p.id = :procedureId")
    List<LieuDeTraitement> findByProcedureId(@Param("procedureId") Long procedureId);
}
