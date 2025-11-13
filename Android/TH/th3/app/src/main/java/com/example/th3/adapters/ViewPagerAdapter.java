package com.example.th3.adapters;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.viewpager2.adapter.FragmentStateAdapter;

import com.example.th3.fragments.GenreListFragment;
import com.example.th3.fragments.MovieListFragment;

public class ViewPagerAdapter extends FragmentStateAdapter {

    public ViewPagerAdapter(@NonNull FragmentActivity fragmentActivity) {
        super(fragmentActivity);
    }

    @NonNull
    @Override
    public Fragment createFragment(int position) {
        switch (position) {
            case 0:
                return new GenreListFragment();
            case 1:
                return new MovieListFragment();
            default:
                return new GenreListFragment();
        }
    }

    @Override
    public int getItemCount() {
        return 2;
    }
}
