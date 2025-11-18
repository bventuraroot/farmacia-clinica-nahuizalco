// Script para manejar la configuración de unidades de medida en productos

$(document).ready(function() {

    function toggleFieldsBySaleType(saleType, isEdit = false) {
        const suffix = isEdit ? '_edit' : '';
        $(`#weight_fields${suffix}`).hide();
        $(`#volume_fields${suffix}`).hide();
        $(`#conversion_info${suffix}`).hide();
        switch(saleType) {
            case 'weight':
                $(`#weight_fields${suffix}`).show();
                $(`#conversion_info${suffix}`).show();
                break;
            case 'volume':
                $(`#volume_fields${suffix}`).show();
                $(`#conversion_info${suffix}`).show();
                break;
        }
    }

    function calculateConversions(isEdit = false) {
        const suffix = isEdit ? '_edit' : '';
        const saleType = $(`#sale_type${suffix}`).val();
        const priceSelector = isEdit ? '#priceedit' : '#price';
        const price = parseFloat($(priceSelector).val()) || 0;
        if (saleType === 'weight') {
            const weightPerUnit = parseFloat($(`#weight_per_unit${suffix}`).val()) || 0;
            if (weightPerUnit > 0 && price > 0) {
                const pricePerLb = price / weightPerUnit;
                const pricePerSack = price;
                const pricePerKg = pricePerLb * 2.2046;
                const valuePerDollar = weightPerUnit / price;
                $(`#price_per_lb${suffix}`).text(`$${pricePerLb.toFixed(2)}`);
                $(`#price_per_sack${suffix}`).text(`$${pricePerSack.toFixed(2)}`);
                $(`#price_per_kg${suffix}`).text(`$${pricePerKg.toFixed(2)}`);
                $(`#value_per_dollar${suffix}`).text(`${valuePerDollar.toFixed(4)} libras`);
            }
        } else if (saleType === 'volume') {
            const volumePerUnit = parseFloat($(`#volume_per_unit${suffix}`).val()) || 0;
            if (volumePerUnit > 0 && price > 0) {
                const pricePerLiter = price / volumePerUnit;
                const pricePerUnit = price;
                const pricePerMl = pricePerLiter / 1000;
                const valuePerDollar = volumePerUnit / price;
                $(`#price_per_lb${suffix}`).text(`$${pricePerLiter.toFixed(2)}`);
                $(`#price_per_sack${suffix}`).text(`$${pricePerUnit.toFixed(2)}`);
                $(`#price_per_kg${suffix}`).text(`$${pricePerMl.toFixed(4)}`);
                $(`#value_per_dollar${suffix}`).text(`${valuePerDollar.toFixed(4)} litros`);
            }
        }
    }

    // Inicialización de eventos (crear)
    $('#sale_type').on('change', function() { toggleFieldsBySaleType($(this).val(), false); calculateConversions(false); });
    $('#weight_per_unit').on('input', function() { calculateConversions(false); });
    $('#volume_per_unit').on('input', function() { calculateConversions(false); });
    $('#price').on('input', function() { calculateConversions(false); validateCreateForm(); });

    function validateCreateForm() {
        const isValid = validateProductFormByType(false);
        const createBtn = $('#addproductForm button[type="submit"]');
        createBtn.prop('disabled', !isValid);
        createBtn.toggleClass('btn-secondary', !isValid).toggleClass('btn-primary', isValid);
    }

    // Inicialización de eventos (editar)
    $('#sale_type_edit').on('change', function() { toggleFieldsBySaleType($(this).val(), true); calculateConversions(true); });
    $('#weight_per_unit_edit').on('input', function() { calculateConversions(true); });
    $('#volume_per_unit_edit').on('input', function() { calculateConversions(true); });
    $('#priceedit').on('input', function() { calculateConversions(true); validateEditForm(); });

    function validateEditForm() {
        const isValid = validateProductFormByType(true);
        //image.png$('#updateProductBtn').prop('disabled', !isValid).toggleClass('btn-secondary', !isValid).toggleClass('btn-primary', isValid);
    }

    if ($('#sale_type').val()) { toggleFieldsBySaleType($('#sale_type').val(), false); calculateConversions(false); validateCreateForm(); }

    $('#updateProductModal').on('hidden.bs.modal', function () {
        $('#sale_type_edit, #weight_per_unit_edit, #volume_per_unit_edit, #priceedit').removeClass('is-invalid');
        //$('#updateProductBtn').prop('disabled', true);
    });

    $('#addProductModal').on('hidden.bs.modal', function () {
        $('#sale_type, #weight_per_unit, #volume_per_unit, #price').removeClass('is-invalid');
        $('#createProductBtn').prop('disabled', true);
    });

    // Cargar datos al abrir modal de edición (función global)
    window.loadProductDataForEdit = function(productData) {
        // Llenar tipo de venta
        if (productData.sale_type) {
            $('#sale_type_edit').val(productData.sale_type);
        }
        // Llenar medidas
        if (productData.weight_per_unit) {
            $('#weight_per_unit_edit').val(productData.weight_per_unit);
        }
        if (productData.volume_per_unit) {
            $('#volume_per_unit_edit').val(productData.volume_per_unit);
        }
        if (productData.content_per_unit) {
            $('#content_per_unit_edit').val(productData.content_per_unit);
        }
        // Mostrar campos y calcular
        const type = productData.sale_type || $('#sale_type_edit').val();
        if (type) {
            toggleFieldsBySaleType(type, true);
        }
        // Asegurar que precio esté en el input correcto
        if (productData.price) {
            $('#priceedit').val(productData.price);
        }
        // Calcular tras poblar
        setTimeout(() => { calculateConversions(true); validateEditForm(); }, 0);
    };
});

function validateProductFormByType(isEdit = false) {
    const suffix = isEdit ? '_edit' : '';
    const saleType = $(`#sale_type${suffix}`).val();
    const priceSelector = isEdit ? '#priceedit' : '#price';
    let isValid = true;
    if (!saleType) { $(`#sale_type${suffix}`).addClass('is-invalid'); isValid = false; } else { $(`#sale_type${suffix}`).removeClass('is-invalid');
        if (saleType === 'weight') { const w = $(`#weight_per_unit${suffix}`).val(); const p = $(priceSelector).val(); if (!w || w <= 0) { $(`#weight_per_unit${suffix}`).addClass('is-invalid'); isValid = false; } else { $(`#weight_per_unit${suffix}`).removeClass('is-invalid'); } if (!p || p <= 0) { $(priceSelector).addClass('is-invalid'); isValid = false; } else { $(priceSelector).removeClass('is-invalid'); } }
        if (saleType === 'volume') { const v = $(`#volume_per_unit${suffix}`).val(); const p = $(priceSelector).val(); if (!v || v <= 0) { $(`#volume_per_unit${suffix}`).addClass('is-invalid'); isValid = false; } else { $(`#volume_per_unit${suffix}`).removeClass('is-invalid'); } if (!p || p <= 0) { $(priceSelector).addClass('is-invalid'); isValid = false; } else { $(priceSelector).removeClass('is-invalid'); } }
    }
    return isValid;
}

window.validateProductForm = function() { return validateProductFormByType(false); };
window.validateProductEditForm = function() { return validateProductFormByType(true); };
