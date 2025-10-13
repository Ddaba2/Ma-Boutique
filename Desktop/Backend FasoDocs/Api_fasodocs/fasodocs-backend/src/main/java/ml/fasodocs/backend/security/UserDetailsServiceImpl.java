package ml.fasodocs.backend.security;

import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.repository.CitoyenRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

/**
 * Service pour charger les détails de l'utilisateur
 */
@Service
public class UserDetailsServiceImpl implements UserDetailsService {

    @Autowired
    private CitoyenRepository citoyenRepository;

    @Override
    @Transactional
    public UserDetails loadUserByUsername(String username) throws UsernameNotFoundException {
        Citoyen citoyen = citoyenRepository.findByEmailOrTelephone(username)
                .orElseThrow(() -> new UsernameNotFoundException("Utilisateur non trouvé avec l'identifiant: " + username));

        return UserDetailsImpl.build(citoyen);
    }
}
