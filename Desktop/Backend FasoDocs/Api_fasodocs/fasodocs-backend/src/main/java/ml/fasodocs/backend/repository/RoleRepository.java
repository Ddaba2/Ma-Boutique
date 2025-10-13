package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Role;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

/**
 * Repository pour l'entité Role
 */
@Repository
public interface RoleRepository extends JpaRepository<Role, Long> {

    /**
     * Recherche un rôle par son nom
     */
    Optional<Role> findByNom(Role.NomRole nom);
}
