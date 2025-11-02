/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package model;

/**
 *
 * @author User
 */
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
