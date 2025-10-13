package ml.fasodocs.backend.service;

import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.entity.HistoriqueLog;
import ml.fasodocs.backend.entity.Procedure;
import ml.fasodocs.backend.repository.CitoyenRepository;
import ml.fasodocs.backend.repository.HistoriqueLogRepository;
import ml.fasodocs.backend.security.UserDetailsImpl;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

/**
 * Service pour la gestion de l'historique des actions
 */
@Service
@Transactional
public class HistoriqueService {

    @Autowired
    private HistoriqueLogRepository historiqueRepository;

    @Autowired
    private CitoyenRepository citoyenRepository;

    /**
     * Enregistre une consultation de procédure
     */
    public void enregistrerConsultation(Procedure procedure) {
        try {
            Citoyen citoyen = getCitoyenConnecte();
            
            HistoriqueLog log = new HistoriqueLog();
            log.setTypeAction("CONSULTATION");
            log.setCitoyen(citoyen);
            log.setProcedure(procedure);
            log.setDetails("Consultation de la procédure: " + procedure.getNom());
            
            historiqueRepository.save(log);
        } catch (Exception e) {
            // Si l'utilisateur n'est pas connecté, on ne sauvegarde pas l'historique
        }
    }

    /**
     * Enregistre une recherche
     */
    public void enregistrerRecherche(String termeRecherche) {
        try {
            Citoyen citoyen = getCitoyenConnecte();
            
            HistoriqueLog log = new HistoriqueLog();
            log.setTypeAction("RECHERCHE");
            log.setCitoyen(citoyen);
            log.setDetails("Recherche: " + termeRecherche);
            
            historiqueRepository.save(log);
        } catch (Exception e) {
            // Si l'utilisateur n'est pas connecté, on ne sauvegarde pas l'historique
        }
    }

    /**
     * Récupère l'historique du citoyen connecté
     */
    public List<HistoriqueLog> obtenirHistoriqueCitoyen() {
        Citoyen citoyen = getCitoyenConnecte();
        return historiqueRepository.findByCitoyenIdOrderByDateActionDesc(citoyen.getId());
    }

    /**
     * Récupère le citoyen connecté
     */
    private Citoyen getCitoyenConnecte() {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();
        
        return citoyenRepository.findById(userDetails.getId())
                .orElseThrow(() -> new RuntimeException("Citoyen non trouvé"));
    }
}
