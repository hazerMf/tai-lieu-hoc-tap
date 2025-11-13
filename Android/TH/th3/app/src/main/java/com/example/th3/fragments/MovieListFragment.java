package com.example.th3.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.th3.R;
import com.example.th3.activities.AddEditMovieActivity;
import com.example.th3.adapters.MovieAdapter;
import com.example.th3.database.AppDatabase;
import com.example.th3.database.MovieWithDetails;

import java.util.Calendar;
import java.util.List;

public class MovieListFragment extends Fragment {
    private RecyclerView recyclerView;
    private MovieAdapter adapter;
    private AppDatabase database;
    private EditText etFromYear, etToYear;
    private Button btnFilter;
    private TextView tvStatistics;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_movie_list, container, false);

        database = AppDatabase.getDatabase(requireContext());
        
        recyclerView = view.findViewById(R.id.recyclerViewMovies);
        etFromYear = view.findViewById(R.id.etFromYear);
        etToYear = view.findViewById(R.id.etToYear);
        btnFilter = view.findViewById(R.id.btnFilter);
        tvStatistics = view.findViewById(R.id.tvStatistics);

        recyclerView.setLayoutManager(new LinearLayoutManager(requireContext()));
        adapter = new MovieAdapter(requireContext(), this::onMovieClick, this::onMovieDelete);
        recyclerView.setAdapter(adapter);

        loadMovies();
        setupFilter();
        updateStatistics();

        return view;
    }

    private void loadMovies() {
        List<MovieWithDetails> movies = database.movieDao().getAllMoviesWithDetails();
        adapter.setMovies(movies);
    }

    private void setupFilter() {
        btnFilter.setOnClickListener(v -> {
            String fromYearStr = etFromYear.getText().toString().trim();
            String toYearStr = etToYear.getText().toString().trim();

            if (fromYearStr.isEmpty() && toYearStr.isEmpty()) {
                loadMovies();
                return;
            }

            try {
                int fromYear = fromYearStr.isEmpty() ? 1900 : Integer.parseInt(fromYearStr);
                int toYear = toYearStr.isEmpty() ? 2100 : Integer.parseInt(toYearStr);

                Calendar calStart = Calendar.getInstance();
                calStart.set(fromYear, Calendar.JANUARY, 1, 0, 0, 0);
                
                Calendar calEnd = Calendar.getInstance();
                calEnd.set(toYear, Calendar.DECEMBER, 31, 23, 59, 59);

                List<MovieWithDetails> filteredMovies = database.movieDao()
                        .getMoviesByDateRange(calStart.getTimeInMillis(), calEnd.getTimeInMillis());
                adapter.setMovies(filteredMovies);
            } catch (NumberFormatException e) {
                // Handle invalid input
                loadMovies();
            }
        });
    }

    private void updateStatistics() {
        int actionCount = database.movieDao().countMoviesByCategory("Hành động");
        int romanceCount = database.movieDao().countMoviesByCategory("Tình cảm");
        int comedyCount = database.movieDao().countMoviesByCategory("Hài hước");
        int horrorCount = database.movieDao().countMoviesByCategory("Kinh dị");

        String stats = String.format("Thống kê:\nHành động: %d | Tình cảm: %d | Hài hước: %d | Kinh dị: %d",
                actionCount, romanceCount, comedyCount, horrorCount);
        tvStatistics.setText(stats);
    }

    private void onMovieClick(MovieWithDetails movieWithDetails) {
        Intent intent = new Intent(requireContext(), AddEditMovieActivity.class);
        intent.putExtra("movieId", movieWithDetails.movie.getId());
        startActivity(intent);
    }

    private void onMovieDelete(MovieWithDetails movieWithDetails) {
        database.movieDao().delete(movieWithDetails.movie);
        loadMovies();
        updateStatistics();
    }

    @Override
    public void onResume() {
        super.onResume();
        loadMovies();
        updateStatistics();
    }
}
