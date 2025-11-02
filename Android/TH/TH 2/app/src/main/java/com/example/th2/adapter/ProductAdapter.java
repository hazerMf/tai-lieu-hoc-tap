package com.example.th2.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.RatingBar;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.th2.R;
import com.example.th2.model.Product;

import java.util.List;

public class ProductAdapter extends RecyclerView.Adapter<ProductAdapter.VH> {

    public interface OnItemAction {
        void onEdit(int position);
        void onDelete(int position);
    }

    private final Context ctx;
    private final List<Product> list;
    private final OnItemAction action;

    public ProductAdapter(Context ctx, List<Product> list, OnItemAction action) {
        this.ctx = ctx;
        this.list = list;
        this.action = action;
    }

    @NonNull
    @Override
    public VH onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View v = LayoutInflater.from(ctx).inflate(R.layout.item_product, parent, false);
        return new VH(v);
    }

    @Override
    public void onBindViewHolder(@NonNull VH holder, int position) {
        Product p = list.get(position);

        // set image + text + rating
        holder.img.setImageResource(p.getImageRes());
        holder.tvName.setText(p.getName());
        holder.tvTypeQty.setText(p.getType() + " • Số lượng: " + p.getQty());
        holder.tvPrice.setText(String.format("%,dđ", p.getPrice()));
        holder.ratingBar.setRating(p.getRating());
        holder.ratingBar.setOnRatingBarChangeListener((bar, rating, fromUser) -> {
            if (fromUser) {
                p.setRating(rating);
            }
        });

        // actions
        holder.btnEdit.setOnClickListener(v -> {
            if (action != null) action.onEdit(position);
        });
        holder.btnDelete.setOnClickListener(v -> {
            if (action != null) action.onDelete(position);
        });
    }

    @Override
    public int getItemCount() {
        return list != null ? list.size() : 0;
    }

    static class VH extends RecyclerView.ViewHolder {
        ImageView img;
        TextView tvName, tvTypeQty, tvPrice;
        RatingBar ratingBar;
        ImageButton btnEdit, btnDelete;

        VH(@NonNull View itemView) {
            super(itemView);
            img = itemView.findViewById(R.id.imgProduct);
            tvName = itemView.findViewById(R.id.tvName);       // match your XML
            tvTypeQty = itemView.findViewById(R.id.tvTypeQty);
            tvPrice = itemView.findViewById(R.id.tvPrice);
            ratingBar = itemView.findViewById(R.id.ratingBar);
            btnEdit = itemView.findViewById(R.id.btnEdit);
            btnDelete = itemView.findViewById(R.id.btnDelete);
        }
    }
}
