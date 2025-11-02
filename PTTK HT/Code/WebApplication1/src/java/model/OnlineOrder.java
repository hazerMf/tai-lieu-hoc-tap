package model;

public class OnlineOrder extends Order {
    private String deliveryAddress;
    private float deliverFee;
    private Staff deliveryStaff;

    public String getDeliveryAddress() {
        return deliveryAddress;
    }

    public void setDeliveryAddress(String deliveryAddress) {
        this.deliveryAddress = deliveryAddress;
    }

    public float getDeliverFee() {
        return deliverFee;
    }

    public void setDeliverFee(float deliverFee) {
        this.deliverFee = deliverFee;
    }

    public Staff getDeliveryStaff() {
        return deliveryStaff;
    }

    public void setDeliveryStaff(Staff deliveryStaff) {
        this.deliveryStaff = deliveryStaff;
    }
    
    
}
