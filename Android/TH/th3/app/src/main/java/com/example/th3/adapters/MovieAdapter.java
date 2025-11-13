package com.example.th3.adapters;

import android.app.AlertDialog;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.th3.R;
import com.example.th3.database.MovieWithDetails;

import java.text.NumberFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class MovieAdapter extends RecyclerView.Adapter<MovieAdapter.ViewHolder> {
    private List<MovieWithDetails> movies = new ArrayList<>();
    private Context context;
    private OnMovieClickListener clickListener;
    private OnMovieDeleteListener deleteListener;

    public interface OnMovieClickListener {
        void onMovieClick(MovieWithDetails movie);
    }

    public interface OnMovieDeleteListener {
        void onMovieDelete(MovieWithDetails movie);
    }

    public MovieAdapter(Context context, OnMovieClickListener clickListener, OnMovieDeleteListener deleteListener) {
        this.context = context;
        this.clickListener = clickListener;
        this.deleteListener = deleteListener;
    }

    public void setMovies(List<MovieWithDetails> movies) {
        this.movies = movies;
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_movie, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        MovieWithDetails movieWithDetails = movies.get(position);
        
        holder.tvMovieName.setText(movieWithDetails.movie.getName());
        holder.tvGenreName.setText("Thể loại: " + (movieWithDetails.genre != null ? movieWithDetails.genre.getName() : "N/A"));
        holder.tvCinemaName.setText("Rạp: " + (movieWithDetails.cinema != null ? movieWithDetails.cinema.getName() : "N/A"));
        
        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        String dateStr = sdf.format(new Date(movieWithDetails.movie.getReleaseDate()));
        holder.tvReleaseDate.setText("Ngày chiếu: " + dateStr);
        
        NumberFormat formatter = NumberFormat.getCurrencyInstance(new Locale("vi", "VN"));
        holder.tvTicketPrice.setText("Giá vé: " + formatter.format(movieWithDetails.movie.getTicketPrice()));

        holder.itemView.setOnClickListener(v -> {
            if (clickListener != null) {
                clickListener.onMovieClick(movieWithDetails);
            }
        });

        holder.btnDelete.setOnClickListener(v -> {
            new AlertDialog.Builder(context)
                    .setTitle("Xác nhận xóa")
                    .setMessage("Bạn có chắc muốn xóa phim \"" + movieWithDetails.movie.getName() + "\"?")
                    .setPositiveButton("Xóa", (dialog, which) -> {
                        if (deleteListener != null) {
                            deleteListener.onMovieDelete(movieWithDetails);
                        }
                    })
                    .setNegativeButton("Hủy", null)
                    .show();
        });
    }

    @Override
    public int getItemCount() {
        return movies.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView tvMovieName, tvGenreName, tvCinemaName, tvReleaseDate, tvTicketPrice;
        ImageButton btnDelete;

        ViewHolder(View itemView) {
            super(itemView);
            tvMovieName = itemView.findViewById(R.id.tvMovieName);
            tvGenreName = itemView.findViewById(R.id.tvGenreName);
            tvCinemaName = itemView.findViewById(R.id.tvCinemaName);
            tvReleaseDate = itemView.findViewById(R.id.tvReleaseDate);
            tvTicketPrice = itemView.findViewById(R.id.tvTicketPrice);
            btnDelete = itemView.findViewById(R.id.btnDelete);
        }
    }
}
