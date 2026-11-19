function rupiah_formater(nominal) {
    var tempNominal = rupiah_format_destroyer(nominal + "");

    var putar = "";
    for (var a = tempNominal.length - 1; a >= 0; a--) {
        putar += tempNominal.charAt(a);
    }

    var plusDot = "";
    for (var b = 1; b < putar.length + 1; b++) {
        plusDot += putar.charAt(b - 1);
        if (b % 3 == 0) {
            plusDot += ".";
        }
    }

    var rupiah = "";
    for (var c = plusDot.length - 1; c >= 0; c--) {
        rupiah += plusDot.charAt(c);
    }

    var first = rupiah.charAt(0) + "";
    var isExecption = 0;
    if (first == ".") {
        isExecption = 1;
    }

    var finalRupiah = "";
    for (var rp = isExecption; rp < rupiah.length; rp++) {
        finalRupiah += rupiah.charAt(rp);
    }

    return finalRupiah;
}

function rupiah_format_destroyer(my_text) {
    var x = my_text + "";
    return x.replace(/\./g, '').replace('Rp', '');
}