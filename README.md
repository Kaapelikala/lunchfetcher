# Install
```
git clone https://github.com/joonaskaskisola/lunchfetcher.git lunchfetcher/
git clone https://github.com/joonaskaskisola/slack-api.git lunchfetcher/slack-api/
```

# Usage
```
require_once '/path/to/Lunch.php';
$l = new Lunch('slack-api-key', '#slack_channel');
```

```
lunchBOT [1:47 PM] 
Lunches for ​*Friday*​
​*Bruuveri*​ Savupaprikakeittoa L,G / Gulassipataa L,G / Saariston lohta VL,G / Vuohenjuusto-tomaatteja L,G
​*Harald*​ Metsänkävijän herkkutattikeittoa / Juuresliemessä haudutettuja rantamuikkuja / Broilerinrintaa vuohenjuustokastin kera
​*KarlJohan*​ KUKKAKAALIKEITTO (L, G) / LIHAMUREKE, SIPULIKASTIKE (L / VL) / KINKKUKIUSAUS (L, G)
​*Olearys*​ Chili con carne, riisiä, kermaviiliä
​*Posti*​ Lohi-kookoscurrya (A, L, M, ​*) / Nuudeleita (A, L, M) / Tilliperunoita (G, L, M, *​) / Juurespihvejä (A, G, L, M, VS, *) / Remouladekastiketta (A, G, L, M) / Juustoista kikhernekeittoa (A, L, VS)
```
