import os

def vypis_slozku(slozka, uroven=0, vystup=[], vynechat={'.git'}):
    for polozka in sorted(os.listdir(slozka)):
        if polozka in vynechat:
            continue

        cesta = os.path.join(slozka, polozka)
        prefix = "    " * uroven + "|-- "
        vystup.append(f"{prefix}{polozka}")

        if os.path.isdir(cesta):
            vypis_slozku(cesta, uroven + 1, vystup, vynechat)
    return vystup

if __name__ == "__main__":
    aktualni_slozka = os.path.dirname(os.path.abspath(__file__))
    vystup = vypis_slozku(aktualni_slozka)

    with open("vypis.txt", "w", encoding="utf-8") as f:
        f.write("\n".join(vystup))

    print("Výpis uložen do vypis.txt (bez .git)")
