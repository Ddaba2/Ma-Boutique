package ml.fasodocs.backend.config;

import ml.fasodocs.backend.entity.Role;
import ml.fasodocs.backend.repository.RoleRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.CommandLineRunner;
import org.springframework.stereotype.Component;

/**
 * Initialise les données de base de l'application
 */
@Component
public class DataInitializer implements CommandLineRunner {

    private static final Logger logger = LoggerFactory.getLogger(DataInitializer.class);

    @Autowired
    private RoleRepository roleRepository;

    @Override
    public void run(String... args) throws Exception {
        initializeRoles();
    }

    /**
     * Initialise les rôles par défaut
     */
    private void initializeRoles() {
        if (roleRepository.count() == 0) {
            Role roleCitoyen = new Role();
            roleCitoyen.setNom(Role.NomRole.ROLE_CITOYEN);
            roleRepository.save(roleCitoyen);

            Role roleAdmin = new Role();
            roleAdmin.setNom(Role.NomRole.ROLE_ADMIN);
            roleRepository.save(roleAdmin);

            logger.info("Rôles initialisés: ROLE_CITOYEN, ROLE_ADMIN");
        }
    }
}
