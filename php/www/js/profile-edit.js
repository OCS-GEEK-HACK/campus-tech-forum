"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const fileInput = document.getElementById("image");
  const hiddenInput = document.getElementById("image-base64");
  const placeholder = document.getElementById("image-placeholder");
  const imagePreview = document.getElementById("image-preview");
  const resetButton = document.getElementById("reset-image");

  // 画像変更時のプレビュー更新
  fileInput.addEventListener("change", (event) => {
    const file = event.target.files[0]; // ファイル取得

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        // Base64をhiddenフィールドに格納
        hiddenInput.value = e.target.result;
        imagePreview.src = e.target.result;
        placeholder.classList.add("d-none")
        imagePreview.classList.remove("d-none")
      };

      reader.readAsDataURL(file); // Base64形式に変換
    }
  });

  // リセットボタンのクリック処理
  resetButton.addEventListener("click", () => {
    hiddenInput.value = ""; // hiddenフィールドをクリア
    fileInput.value = ""; // ファイル入力をクリア

    placeholder.classList.remove("d-none")
    imagePreview.classList.add("d-none")
  });
});
