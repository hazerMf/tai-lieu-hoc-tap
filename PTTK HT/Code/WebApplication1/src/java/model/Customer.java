package model;

public class Customer extends User {
    private int customerId;

    public Customer() {
        super(); // call User() constructor
    }

    public Customer(int customerId, int id, String name, String password, String address,
                    String email, String phoneNumber, String role) {
        super(id, name, password, address, email, phoneNumber, role);
        this.customerId = customerId;
    }

    public int getCustomerId() {
        return customerId;
    }

    public void setCustomerId(int customerId) {
        this.customerId = customerId;
    }
}
