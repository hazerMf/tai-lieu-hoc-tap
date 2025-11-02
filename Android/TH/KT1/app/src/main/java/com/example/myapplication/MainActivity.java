package com.example.datvemaybay;

import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.RadioGroup;
import android.widget.RatingBar;
import android.widget.TextView;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import java.text.NumberFormat;
import java.util.Locale;

public class MainActivity extends AppCompatActivity {

    private static final int GIA_VE_HANG_NHAT = 1500000;
    private static final int GIA_VE_THUONG_GIA = 1300000;
    private static final int GIA_VE_PHO_THONG = 1000000;

    private TextInputLayout layoutHoTen, layoutDienThoai, layoutSoLuong;
    private TextInputEditText inputHoTen, inputDienThoai, inputSoLuong, inputUuDai;
    private RadioGroup radioGroupLoaiVe;
    private RatingBar ratingBarDanhGia;
    private Button buttonDatVe;
    private TextView textViewKetQua;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        anhXaViews();

        buttonDatVe.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                xuLyDatVe();
            }
        });
    }

    private void anhXaViews() {
        layoutHoTen = findViewById(R.id.layout_ho_ten);
        layoutDienThoai = findViewById(R.id.layout_dien_thoai);
        layoutSoLuong = findViewById(R.id.layout_so_luong);
        inputHoTen = findViewById(R.id.input_ho_ten);
        inputDienThoai = findViewById(R.id.input_dien_thoai);
        inputSoLuong = findViewById(R.id.input_so_luong);
        inputUuDai = findViewById(R.id.input_uu_dai);
        radioGroupLoaiVe = findViewById(R.id.radio_group_loai_ve);
        ratingBarDanhGia = findViewById(R.id.rating_bar_danh_gia);
        buttonDatVe = findViewById(R.id.button_dat_ve);
        textViewKetQua = findViewById(R.id.text_view_ket_qua);
    }

    private void xuLyDatVe() {
        layoutHoTen.setError(null);
        layoutDienThoai.setError(null);
        layoutSoLuong.setError(null);

        String hoTen = inputHoTen.getText().toString().trim();
        String dienThoai = inputDienThoai.getText().toString().trim();
        String soLuongText = inputSoLuong.getText().toString();

        if (!validateInput(hoTen, dienThoai, soLuongText)) {
            textViewKetQua.setVisibility(View.GONE);
            return;
        }

        int soLuong = Integer.parseInt(soLuongText);
        double uuDaiPhanTram = 0;
        try {
            uuDaiPhanTram = Double.parseDouble(inputUuDai.getText().toString());
        } catch (NumberFormatException e) {
            uuDaiPhanTram = 0;
        }

        int selectedRadioId = radioGroupLoaiVe.getCheckedRadioButtonId();
        int giaVeDonVi;
        if (selectedRadioId == R.id.radio_hang_nhat) {
            giaVeDonVi = GIA_VE_HANG_NHAT;
        } else if (selectedRadioId == R.id.radio_thuong_gia) {
            giaVeDonVi = GIA_VE_THUONG_GIA;
        } else {
            giaVeDonVi = GIA_VE_PHO_THONG;
        }

        double tongTienBanDau = (double) giaVeDonVi * soLuong;
        double tienGiamUuDai = tongTienBanDau * (uuDaiPhanTram / 100);
        double tongTienSauUuDai = tongTienBanDau - tienGiamUuDai;

        if (ratingBarDanhGia.getRating() == 5.0f) {
            double tienGiamThem = tongTienSauUuDai * 0.05;
            tongTienSauUuDai -= tienGiamThem;
        }

        Locale localeVN = new Locale("vi", "VN");
        NumberFormat currencyFormatter = NumberFormat.getCurrencyInstance(localeVN);
        String tongTienFormatted = currencyFormatter.format(tongTienSauUuDai);

        String ketQuaText = "THÔNG TIN ĐẶT VÉ THÀNH CÔNG\n" +
                "-----------------------------------\n" +
                "Họ và Tên: " + hoTen + "\n" +
                "Số điện thoại: " + dienThoai + "\n" +
                "Tổng tiền: " + tongTienFormatted;

        textViewKetQua.setText(ketQuaText);
        textViewKetQua.setVisibility(View.VISIBLE);
    }

    private boolean validateInput(String hoTen, String dienThoai, String soLuongText) {
        boolean isValid = true;
        if (hoTen.isEmpty()) {
            layoutHoTen.setError("Vui lòng nhập họ và tên");
            isValid = false;
        }
        if (dienThoai.isEmpty()) {
            layoutDienThoai.setError("Vui lòng nhập số điện thoại");
            isValid = false;
        }
        try {
            int soLuong = Integer.parseInt(soLuongText);
            if (soLuong <= 0) {
                layoutSoLuong.setError("Số lượng vé phải lớn hơn 0");
                isValid = false;
            }
        } catch (NumberFormatException e) {
            layoutSoLuong.setError("Số lượng vé không hợp lệ");
            isValid = false;
        }
        return isValid;
    }
}
