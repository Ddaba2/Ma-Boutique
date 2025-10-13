package ml.fasodocs.backend.security;

import ml.fasodocs.backend.entity.Citoyen;
import com.fasterxml.jackson.annotation.JsonIgnore;
import lombok.AllArgsConstructor;
import lombok.Data;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;

import java.util.Collection;
import java.util.List;
import java.util.stream.Collectors;

/**
 * Implémentation de UserDetails pour Spring Security
 */
@Data
@AllArgsConstructor
public class UserDetailsImpl implements UserDetails {

    private static final long serialVersionUID = 1L;

    private Long id;
    private String nom;
    private String prenom;
    private String email;
    private String telephone;

    @JsonIgnore
    private String password;

    private Collection<? extends GrantedAuthority> authorities;

    /**
     * Construit un UserDetailsImpl à partir d'un Citoyen
     */
    public static UserDetailsImpl build(Citoyen citoyen) {
        List<GrantedAuthority> authorities = citoyen.getRoles().stream()
                .map(role -> new SimpleGrantedAuthority(role.getNom().name()))
                .collect(Collectors.toList());

        return new UserDetailsImpl(
                citoyen.getId(),
                citoyen.getNom(),
                citoyen.getPrenom(),
                citoyen.getEmail(),
                citoyen.getTelephone(),
                citoyen.getMotDePasse(),
                authorities);
    }

    @Override
    public Collection<? extends GrantedAuthority> getAuthorities() {
        return authorities;
    }

    @Override
    public String getPassword() {
        return password;
    }

    @Override
    public String getUsername() {
        return email != null ? email : telephone;
    }

    @Override
    public boolean isAccountNonExpired() {
        return true;
    }

    @Override
    public boolean isAccountNonLocked() {
        return true;
    }

    @Override
    public boolean isCredentialsNonExpired() {
        return true;
    }

    @Override
    public boolean isEnabled() {
        return true;
    }
}
