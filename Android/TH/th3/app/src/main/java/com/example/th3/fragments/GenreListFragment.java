package com.example.th3.fragments;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.SearchView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.th3.R;
import com.example.th3.adapters.GenreAdapter;
import com.example.th3.database.AppDatabase;
import com.example.th3.database.Genre;

import java.util.ArrayList;
import java.util.List;

public class GenreListFragment extends Fragment {
    private RecyclerView recyclerView;
    private GenreAdapter adapter;
    private AppDatabase database;
    private SearchView searchView;
    private CheckBox cbAction, cbRomance, cbComedy, cbHorror;
    private List<Genre> allGenres;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_genre_list, container, false);

        database = AppDatabase.getDatabase(requireContext());
        
        recyclerView = view.findViewById(R.id.recyclerViewGenres);
        searchView = view.findViewById(R.id.searchViewGenre);
        cbAction = view.findViewById(R.id.cbAction);
        cbRomance = view.findViewById(R.id.cbRomance);
        cbComedy = view.findViewById(R.id.cbComedy);
        cbHorror = view.findViewById(R.id.cbHorror);

        recyclerView.setLayoutManager(new LinearLayoutManager(requireContext()));
        adapter = new GenreAdapter();
        recyclerView.setAdapter(adapter);

        loadGenres();
        setupSearch();
        setupFilters();

        return view;
    }

    private void loadGenres() {
        allGenres = database.genreDao().getAllGenres();
        adapter.setGenres(allGenres);
    }

    private void setupSearch() {
        searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
            @Override
            public boolean onQueryTextSubmit(String query) {
                filterGenres();
                return true;
            }

            @Override
            public boolean onQueryTextChange(String newText) {
                filterGenres();
                return true;
            }
        });
    }

    private void setupFilters() {
        View.OnClickListener filterListener = v -> filterGenres();
        cbAction.setOnClickListener(filterListener);
        cbRomance.setOnClickListener(filterListener);
        cbComedy.setOnClickListener(filterListener);
        cbHorror.setOnClickListener(filterListener);
    }

    private void filterGenres() {
        String searchText = searchView.getQuery().toString().trim();
        List<String> selectedCategories = new ArrayList<>();
        
        if (cbAction.isChecked()) selectedCategories.add("Hành động");
        if (cbRomance.isChecked()) selectedCategories.add("Tình cảm");
        if (cbComedy.isChecked()) selectedCategories.add("Hài hước");
        if (cbHorror.isChecked()) selectedCategories.add("Kinh dị");

        List<Genre> filteredList = new ArrayList<>();
        
        for (Genre genre : allGenres) {
            // Check if name matches search text
            boolean matchesSearch = searchText.isEmpty() || 
                    genre.getName().toLowerCase().contains(searchText.toLowerCase());
            
            // Check if genre matches ALL selected categories (AND logic)
            // Example: If "Action" checked → show genres with "Hành động"
            //          If "Action" + "Romance" checked → show ONLY genres with BOTH "Hành động" AND "Tình cảm"
            boolean matchesCategory = selectedCategories.isEmpty(); // If no checkbox selected, show all
            
            if (!matchesCategory) {
                // Check if genre contains ALL of the selected categories
                matchesCategory = true; // Assume it matches all
                for (String category : selectedCategories) {
                    if (!genre.getCategories().contains(category)) {
                        matchesCategory = false; // Missing one category, doesn't match
                        break; // No need to check other categories
                    }
                }
            }
            
            // Add genre if it matches both search AND category filter
            if (matchesSearch && matchesCategory) {
                filteredList.add(genre);
            }
        }
        
        adapter.setGenres(filteredList);
    }

    @Override
    public void onResume() {
        super.onResume();
        loadGenres();
    }
}
