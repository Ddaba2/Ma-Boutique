package ml.fasodocs.backend.service;

import ml.fasodocs.backend.dto.request.InscriptionRequest;
import ml.fasodocs.backend.dto.response.MessageResponse;
import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.entity.Role;
import ml.fasodocs.backend.repository.CitoyenRepository;
import ml.fasodocs.backend.repository.RoleRepository;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.util.Optional;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.*;

/**
 * Tests unitaires pour le service AuthService
 */
@ExtendWith(MockitoExtension.class)
class AuthServiceTest {

    @Mock
    private CitoyenRepository citoyenRepository;

    @Mock
    private RoleRepository roleRepository;

    @Mock
    private PasswordEncoder passwordEncoder;

    @Mock
    private EmailService emailService;

    @InjectMocks
    private AuthService authService;

    private InscriptionRequest inscriptionRequest;
    private Role roleCitoyen;

    @BeforeEach
    void setUp() {
        inscriptionRequest = new InscriptionRequest();
        inscriptionRequest.setNom("Traoré");
        inscriptionRequest.setPrenom("Amadou");
        inscriptionRequest.setEmail("amadou.traore@example.com");
        inscriptionRequest.setTelephone("76123456");
        inscriptionRequest.setMotDePasse("Password123");
        inscriptionRequest.setLanguePreferee("fr");

        roleCitoyen = new Role();
        roleCitoyen.setId(1L);
        roleCitoyen.setNom(Role.NomRole.ROLE_CITOYEN);
    }

    @Test
    void testInscriptionReussie() {
        // Given
        when(citoyenRepository.existsByEmail(anyString())).thenReturn(false);
        when(citoyenRepository.existsByTelephone(anyString())).thenReturn(false);
        when(roleRepository.findByNom(Role.NomRole.ROLE_CITOYEN)).thenReturn(Optional.of(roleCitoyen));
        when(passwordEncoder.encode(anyString())).thenReturn("encodedPassword");
        when(citoyenRepository.save(any(Citoyen.class))).thenReturn(new Citoyen());

        // When
        MessageResponse response = authService.inscrireCitoyen(inscriptionRequest);

        // Then
        assertTrue(response.isSuccess());
        assertEquals("Inscription réussie! Veuillez vérifier votre email.", response.getMessage());
        verify(citoyenRepository, times(1)).save(any(Citoyen.class));
        verify(emailService, times(1)).envoyerEmailVerification(anyString(), anyString());
    }

    @Test
    void testInscriptionEmailExisteDeja() {
        // Given
        when(citoyenRepository.existsByEmail(anyString())).thenReturn(true);

        // When
        MessageResponse response = authService.inscrireCitoyen(inscriptionRequest);

        // Then
        assertFalse(response.isSuccess());
        assertEquals("Erreur: L'email est déjà utilisé!", response.getMessage());
        verify(citoyenRepository, never()).save(any(Citoyen.class));
    }

    @Test
    void testInscriptionTelephoneExisteDeja() {
        // Given
        when(citoyenRepository.existsByEmail(anyString())).thenReturn(false);
        when(citoyenRepository.existsByTelephone(anyString())).thenReturn(true);

        // When
        MessageResponse response = authService.inscrireCitoyen(inscriptionRequest);

        // Then
        assertFalse(response.isSuccess());
        assertEquals("Erreur: Le téléphone est déjà utilisé!", response.getMessage());
        verify(citoyenRepository, never()).save(any(Citoyen.class));
    }

    @Test
    void testInscriptionSansEmailNiTelephone() {
        // Given
        inscriptionRequest.setEmail(null);
        inscriptionRequest.setTelephone(null);

        // When
        MessageResponse response = authService.inscrireCitoyen(inscriptionRequest);

        // Then
        assertFalse(response.isSuccess());
        assertEquals("Erreur: Email ou téléphone requis!", response.getMessage());
        verify(citoyenRepository, never()).save(any(Citoyen.class));
    }

    @Test
    void testVerificationEmailReussie() {
        // Given
        String codeVerification = "test-code-123";
        Citoyen citoyen = new Citoyen();
        citoyen.setId(1L);
        citoyen.setNom("Traoré");
        citoyen.setPrenom("Amadou");
        citoyen.setEmailVerifie(false);
        citoyen.setCodeVerification(codeVerification);

        when(citoyenRepository.findByCodeVerification(codeVerification)).thenReturn(Optional.of(citoyen));
        when(citoyenRepository.save(any(Citoyen.class))).thenReturn(citoyen);

        // When
        MessageResponse response = authService.verifierEmail(codeVerification);

        // Then
        assertTrue(response.isSuccess());
        assertEquals("Email vérifié avec succès!", response.getMessage());
        assertTrue(citoyen.getEmailVerifie());
        assertNull(citoyen.getCodeVerification());
        verify(citoyenRepository, times(1)).save(citoyen);
    }

    @Test
    void testVerificationEmailCodeInvalide() {
        // Given
        String codeInvalide = "code-invalide";
        when(citoyenRepository.findByCodeVerification(codeInvalide)).thenReturn(Optional.empty());

        // When & Then
        assertThrows(RuntimeException.class, () -> authService.verifierEmail(codeInvalide));
    }
}
