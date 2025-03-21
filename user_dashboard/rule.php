If hb = Anemia Ringan AND lila = Berisiko AND jumlah anak = Berisiko THEN class = STUNTING  
If hb = Anemia Ringan AND lila = Berisiko AND jumlah anak = Tidak Berisiko THEN class = NORMAL  
If hb = Anemia Ringan AND lila = Tidak Berisiko THEN class = NORMAL  

If hb = Anemia Sedang AND usia = Berisiko THEN class = STUNTING  
If hb = Anemia Sedang AND usia = Tidak Berisiko THEN class = NORMAL  

If hb = Normal AND tekanan darah = Hipertensi AND tinggi badan ibu = Pendek AND usia = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipertensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND jumlah anak = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipertensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND jumlah anak = Tidak Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipertensi AND tinggi badan ibu = Tinggi THEN class = NORMAL  

If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Berisiko AND lila = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Berisiko AND lila = Tidak Berisiko AND anc = Kurang THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Berisiko AND lila = Tidak Berisiko AND anc = Lengkap THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND anc = Kurang AND jumlah anak = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND anc = Kurang AND jumlah anak = Tidak Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND anc = Lengkap AND jumlah anak = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND anc = Lengkap AND jumlah anak = Tidak Berisiko AND lila = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Pendek AND usia = Tidak Berisiko AND anc = Lengkap AND jumlah anak = Tidak Berisiko AND lila = Tidak Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Tinggi AND jumlah anak = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Tinggi AND jumlah anak = Tidak Berisiko AND lila = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Hipotensi AND tinggi badan ibu = Tinggi AND jumlah anak = Tidak Berisiko AND lila = Tidak Berisiko THEN class = NORMAL  

If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Berisiko AND usia = Berisiko AND anc = Kurang THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Berisiko AND usia = Berisiko AND anc = Lengkap THEN class = NORMAL  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Berisiko AND usia = Tidak Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Tidak Berisiko AND usia = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Tidak Berisiko AND usia = Tidak Berisiko AND anc = Kurang THEN class = NORMAL  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Tidak Berisiko AND usia = Tidak Berisiko AND anc = Lengkap AND jumlah anak = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Pendek AND lila = Tidak Berisiko AND usia = Tidak Berisiko AND anc = Lengkap AND jumlah anak = Tidak Berisiko THEN class = STUNTING  

If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Kurang AND usia = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Kurang AND usia = Tidak Berisiko AND lila = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Kurang AND usia = Tidak Berisiko AND lila = Tidak Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Lengkap AND lila = Berisiko THEN class = NORMAL  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Lengkap AND lila = Tidak Berisiko AND jumlah anak = Berisiko THEN class = STUNTING  
If hb = Normal AND tekanan darah = Normal AND ttd = Cukup AND tinggi badan ibu = Tinggi AND anc = Lengkap AND lila = Tidak Berisiko AND jumlah anak = Tidak Berisiko THEN class = NORMAL  

If hb = Normal AND tekanan darah = Normal AND ttd = Kurang THEN class = STUNTING  
