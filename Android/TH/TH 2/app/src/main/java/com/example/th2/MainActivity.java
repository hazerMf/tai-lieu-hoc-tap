package com.example.th2;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Bundle;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RatingBar;
import android.widget.Spinner;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

import com.example.th2.adapter.ProductAdapter;
import com.example.th2.model.Product;

public class MainActivity extends AppCompatActivity {

    private RecyclerView recycler;
    private ProductAdapter adapter;
    private List<Product> productList = new ArrayList<>();
    private TextView tvStats;
    private Button btnAdd, btnStats;

    private int counterA = 0, counterQ = 0, counterP = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        recycler = findViewById(R.id.recycler);
        tvStats = findViewById(R.id.tvStats);
        btnAdd = findViewById(R.id.btnAdd);
        btnStats = findViewById(R.id.btnStats);

        adapter = new ProductAdapter(this, productList, new ProductAdapter.OnItemAction() {
            @Override
            public void onEdit(int position) {
                showAddEditDialog(false, position);
            }

            @Override
            public void onDelete(int position) {
                productList.remove(position);
                adapter.notifyItemRemoved(position);
                updateStats();
            }
        });

        recycler.setLayoutManager(new LinearLayoutManager(this));
        recycler.setAdapter(adapter);

        preloadSample();
        btnAdd.setOnClickListener(v -> showAddEditDialog(true, -1));
        btnStats.setOnClickListener(v -> showStatsDialog());
        updateStats();
    }

    private void preloadSample() {
        productList.add(new Product(genId("Áo"), "Áo Cổ Trụ Nam", "Áo", 450000, 4, 4f, R.drawable.shirt));
        productList.add(new Product(genId("Quần"), "Quần Jean", "Quần", 350000, 6, 3.5f, R.drawable.pants));
        productList.add(new Product(genId("Phụ kiện"), "Mũ lưỡi trai", "Phụ kiện", 120000, 10, 4.5f, R.drawable.accessories));
    }

    private String genId(String type) {
        if (type.equals("Áo")) return String.format("A%03d", ++counterA);
        if (type.equals("Quần")) return String.format("Q%03d", ++counterQ);
        return String.format("P%03d", ++counterP);
    }

    private void showAddEditDialog(boolean isAdd, int position) {
        AlertDialog.Builder b = new AlertDialog.Builder(this);
        View v = LayoutInflater.from(this).inflate(R.layout.dialog_product, null);
        EditText etName = v.findViewById(R.id.etName);
        Spinner spinnerType = v.findViewById(R.id.spinnerType);
        EditText etPrice = v.findViewById(R.id.etPrice);
        EditText etQty = v.findViewById(R.id.etQty);
        RatingBar ratingInput = v.findViewById(R.id.ratingInput);
        Spinner spinnerImage = v.findViewById(R.id.spinnerImage);

        // Spinner loại
        String[] types = {"Áo", "Quần", "Phụ kiện"};
        ArrayAdapter<String> typeAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, types);
        typeAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerType.setAdapter(typeAdapter);

        // Spinner hình ảnh
        String[] imgs = {"Hình 1", "Hình 2", "Hình 3"};
        ArrayAdapter<String> imgAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, imgs);
        imgAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerImage.setAdapter(imgAdapter);

        // Nếu sửa, nạp dữ liệu
        if (!isAdd) {
            Product p = productList.get(position);
            etName.setText(p.getName());
            etPrice.setText(String.valueOf(p.getPrice()));
            etQty.setText(String.valueOf(p.getQty()));
            ratingInput.setRating(p.getRating());

            spinnerType.setSelection(p.getType().equals("Quần") ? 1 : p.getType().equals("Phụ kiện") ? 2 : 0);
            if (p.getImageRes() == R.drawable.pants)
                spinnerImage.setSelection(1);
            else if (p.getImageRes() == R.drawable.accessories)
                spinnerImage.setSelection(2);
        }

        b.setView(v);
        b.setTitle(isAdd ? "Thêm sản phẩm" : "Sửa sản phẩm");
        b.setPositiveButton("Lưu", (dialog, which) -> {
            String name = etName.getText().toString().trim();
            String type = spinnerType.getSelectedItem().toString();
            String priceStr = etPrice.getText().toString().trim();
            String qtyStr = etQty.getText().toString().trim();
            float rating = ratingInput.getRating();
            int imgSel = spinnerImage.getSelectedItemPosition();

            if (TextUtils.isEmpty(name) || TextUtils.isEmpty(priceStr) || TextUtils.isEmpty(qtyStr)) return;

            int price = Integer.parseInt(priceStr);
            int qty = Integer.parseInt(qtyStr);
            int imgRes = (imgSel == 1) ? R.drawable.pants : (imgSel == 2) ? R.drawable.accessories : R.drawable.shirt;

            if (isAdd) {
                String id = genId(type);
                Product newP = new Product(id, name, type, price, qty, rating, imgRes);
                productList.add(0, newP);
                adapter.notifyItemInserted(0);
            } else {
                Product p = productList.get(position);
                p.setName(name);
                p.setType(type);
                p.setPrice(price);
                p.setQty(qty);
                p.setRating(rating);
                p.setImageRes(imgRes);
                adapter.notifyItemChanged(position);
            }
            updateStats();
        });
        b.setNegativeButton("Hủy", null);
        b.show();
    }

    private void showStatsDialog() {
        int total = productList.size(), a = 0, q = 0, p = 0;
        Product top = null;
        for (Product prod : productList) {
            switch (prod.getType()) {
                case "Áo": a++; break;
                case "Quần": q++; break;
                default: p++; break;
            }
            if (top == null || prod.getRating() > top.getRating()) top = prod;
        }

        String msg = String.format(
                "Tổng số sản phẩm: %d\nÁo: %d\nQuần: %d\nPhụ kiện: %d\n\nSản phẩm có rating cao nhất:\n%s (%.1f★)",
                total, a, q, p,
                top == null ? "-" : top.getName(),
                top == null ? 0f : top.getRating()
        );

        new AlertDialog.Builder(this)
                .setTitle("Thống kê sản phẩm")
                .setMessage(msg)
                .setPositiveButton("Đóng", null)
                .show();
    }

    private void updateStats() {
        int total = productList.size();
        int a = 0, q = 0, p = 0;
        Product top = null;
        for (Product prod : productList) {
            if (prod.getType().equals("Áo")) a++;
            else if (prod.getType().equals("Quần")) q++;
            else p++;
            if (top == null || prod.getRating() > top.getRating()) top = prod;
        }
        String topStr = top == null ? "-" : top.getName() + " (" + top.getRating() + "★)";
        tvStats.setText(String.format("Tổng %d | Áo %d • Quần %d • Phụ kiện %d | Top: %s", total, a, q, p, topStr));
        tvStats = findViewById(R.id.tvStats);
        tvStats.setVisibility(View.GONE);

    }
}
