<table class="form-table">
    <thead>
        <tr>
            <th scope="col">
                <h2 class="m0 p0">
                    Press button to seed DB with countries, valutes, cities, weather and people.
                </h2>
            </th>

        </tr>
    </thead>
    <tbody>
        <tr>

            <td data-label="Seed" class="tac fomrActivateCodeKey">
                <form method="POST" id="gambingFormSeed" action="<?php echo esc_html(admin_url('admin-ajax.php')) ?>">
                    <?php wp_nonce_field('admin_DB_seed_nonce'); ?>

                    <input id="seed" class="dn" name="seed" type="checkbox" checked />

                    <button class="seedButton" id="seedButton"> Seed </button>
                </form>
            </td>
        </tr>
    </tbody>
</table>