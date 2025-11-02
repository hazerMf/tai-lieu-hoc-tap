package model;

public class ImportItem {
    private int quantity;
    private float price;
    private ImportInvoice invoice;
    private Item item;

    public int getQuantity() {
        return quantity;
    }

    public void setQuantity(int quantity) {
        this.quantity = quantity;
    }

    public float getPrice() {
        return price;
    }

    public void setPrice(float price) {
        this.price = price;
    }

    public ImportInvoice getInvoice() {
        return invoice;
    }

    public void setInvoice(ImportInvoice invoice) {
        this.invoice = invoice;
    }

    public Item getItem() {
        return item;
    }

    public void setItem(Item item) {
        this.item = item;
    }
    
    
}
