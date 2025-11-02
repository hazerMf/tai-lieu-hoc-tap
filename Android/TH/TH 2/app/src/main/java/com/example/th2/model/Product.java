package com.example.th2.model;

public class Product {
    private String productId; // e.g. A001
    private String name;
    private String type; // "Áo","Quần","Phụ kiện"
    private int price; // lưu bằng số (VND)
    private int qty;
    private float rating;
    private int imageRes; // drawable id

    public Product(String productId, String name, String type, int price, int qty, float rating, int imageRes) {
        this.productId = productId;
        this.name = name;
        this.type = type;
        this.price = price;
        this.qty = qty;
        this.rating = rating;
        this.imageRes = imageRes;
    }

    // getters & setters
    public String getProductId() { return productId; }
    public String getName() { return name; }
    public String getType() { return type; }
    public int getPrice() { return price; }
    public int getQty() { return qty; }
    public float getRating() { return rating; }
    public int getImageRes() { return imageRes; }

    public void setName(String name) { this.name = name; }
    public void setType(String type) { this.type = type; }
    public void setPrice(int price) { this.price = price; }
    public void setQty(int qty) { this.qty = qty; }
    public void setRating(float rating) { this.rating = rating; }
    public void setImageRes(int imageRes) { this.imageRes = imageRes; }
}
