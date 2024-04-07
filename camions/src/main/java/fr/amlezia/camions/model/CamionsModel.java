package fr.amlezia.camions.model;

import jakarta.persistence.*;

@Entity
@Table(name = "camion")
public class CamionsModel {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "plaque_immatriculation")
    private String plaqueImmatriculation;

    private int capacite;

    @Column(name = "tournee_id")
    private Long tourneeId;

    public CamionsModel() {
    }

    public CamionsModel(String plaqueImmatriculation, int capacite, Long tourneeId) {
        this.plaqueImmatriculation = plaqueImmatriculation;
        this.capacite = capacite;
        this.tourneeId = tourneeId;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getPlaqueImmatriculation() {
        return plaqueImmatriculation;
    }

    public void setPlaqueImmatriculation(String plaqueImmatriculation) {
        this.plaqueImmatriculation = plaqueImmatriculation;
    }

    public int getCapacite() {
        return capacite;
    }

    public void setCapacite(int capacite) {
        this.capacite = capacite;
    }

    public Long getTourneeId() {
        return tourneeId;
    }

    public void setTourneeId(Long tourneeId) {
        this.tourneeId = tourneeId;
    }

    //pour le débogage
    @Override
    public String toString() {
        return "CamionsModel{" +
                "id=" + id +
                ", plaqueImmatriculation='" + plaqueImmatriculation + '\'' +
                ", capacite=" + capacite +
                ", tourneeId=" + tourneeId +
                '}';
    }
}
