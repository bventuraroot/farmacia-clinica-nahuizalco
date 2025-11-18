// Inventario: manejo de unidades al ingresar producto
(function() {
  // Unidades permitidas según el tipo de producto
  const WEIGHT_UNITS = ['59','36','99']; // Unidad, Libra, Dólar
  const VOLUME_UNITS = ['59','23','99']; // Unidad, Litro, Dólar
  const UNIT_UNITS = ['59','36','99'];   // Unidad, Libra, Dólar
  function pretty(code, name){
    const unitNames = {
      '59': 'Unidad',
      '36': 'Libra',
      '23': 'Litro',
      '99': 'Dólar'
    };
    return unitNames[code] || name || code;
  }
  function loadUnits(productId){
    if(!productId) return;
    $.getJSON('/sale/getproductbyid/' + productId, function(resp){
      if(!(resp && resp.success)) {
        return;
      }
      // Determinar qué unidades mostrar según el tipo de producto
      let allowedUnits = WEIGHT_UNITS; // Por defecto
      if(resp.data.product && resp.data.product.sale_type) {
        switch(resp.data.product.sale_type) {
          case 'volume':
            allowedUnits = VOLUME_UNITS;
            break;
          case 'weight':
            allowedUnits = WEIGHT_UNITS;
            break;
          case 'unit':
            allowedUnits = UNIT_UNITS;
            break;
        }
      }
      const units = (resp.data.units || []).filter(u => allowedUnits.includes(u.unit_code));
      const sel = $('#unit-select');
      sel.empty().append('<option value="">Seleccionar unidad...</option>');
      units.forEach(u=>{
        sel.append(`<option value="${u.unit_code}" data-id="${u.unit_id}" data-factor="${u.conversion_factor}">${pretty(u.unit_code,u.unit_name)}</option>`);
      });
      if(units.length){
        sel.val(units[0].unit_code).trigger('change');
      }
    });
  }
  function updateBasePreview(){
    const qty = parseFloat($('#quantity').val() || '0');
    const opt = $('#unit-select option:selected');
    const unitCode = opt.val();
    const unitId = opt.data('id') || '';

    // Obtener información del producto seleccionado
    const productId = $('#productid').val();
    let baseAdd = 0;
    let factor = 1;

    if (productId && unitCode) {
      // Obtener información del producto para calcular conversión correcta
      $.getJSON('/sale/getproductbyid/' + productId, function(resp) {
        if (resp && resp.success && resp.data.product) {
          const product = resp.data.product;

          if (product.sale_type === 'weight' && product.weight_per_unit) {
            if (unitCode === '59') {
              // Si es unidad (saco), usar directamente como base
              factor = 1;
              baseAdd = qty;
            } else if (unitCode === '36') {
              // Si es libra, usar directamente
              factor = 1;
              baseAdd = qty;
            } else {
              // Para otras unidades, usar factor por defecto
              factor = parseFloat(opt.data('factor') || '1');
              baseAdd = qty * factor;
            }
          } else if (product.sale_type === 'volume' && product.volume_per_unit) {
            if (unitCode === '59') {
              // Si es unidad (galón), convertir a litros
              factor = product.volume_per_unit;
              baseAdd = qty * factor;
            } else if (unitCode === '23') {
              // Si es litro, usar directamente
              factor = 1;
              baseAdd = qty;
            } else {
              // Para otras unidades, usar factor por defecto
              factor = parseFloat(opt.data('factor') || '1');
              baseAdd = qty * factor;
            }
          } else {
            // Para productos por unidad, no hay conversión
            factor = 1;
            baseAdd = qty;
          }

          // Actualizar campos
          $('#conversion-factor').val(factor);
          $('#selected-unit-id').val(unitId);

          if (qty > 0) {
            let baseUnit = 'unidad';
            if (product.sale_type === 'volume') {
              baseUnit = 'litros';
            } else if (product.sale_type === 'weight') {
              baseUnit = unitCode === '59' ? 'sacos' : 'libras';
            }
            $('#base-add-display').text(baseAdd.toFixed(4) + ' ' + baseUnit);
            $('#inventory-conversion-info').show();
          } else {
            $('#inventory-conversion-info').hide();
          }
        }
      });
    } else {
      // Fallback si no hay producto seleccionado
      factor = parseFloat(opt.data('factor') || '1');
      baseAdd = qty * factor;
      $('#conversion-factor').val(factor);
      $('#selected-unit-id').val(unitId);

      if (qty > 0) {
        $('#base-add-display').text(baseAdd.toFixed(4));
        $('#inventory-conversion-info').show();
      } else {
        $('#inventory-conversion-info').hide();
      }
    }
  }
  // Eventos para modal de agregar
  $(document).on('change', '#psearch', function(){ loadUnits($(this).val()); });
  $(document).on('change', '#unit-select', updateBasePreview);
  $(document).on('input', '#quantity', updateBasePreview);
  // Eventos para modal de editar
  $(document).on('change', '#edit_unit-select', updateEditBasePreview);
  $(document).on('input', '#edit_quantity', updateEditBasePreview);
  // Cuando se abre el modal de agregar, si ya hay producto seleccionado, cargar unidades
  $('#addinventoryModal').on('shown.bs.modal', function(){ const pid = $('#productid').val(); if(pid) loadUnits(pid); });
  // Función para actualizar preview en modal de editar
  function updateEditBasePreview(){
    const qty = parseFloat($('#edit_quantity').val() || '0');
    const opt = $('#edit_unit-select option:selected');
    const unitCode = opt.val();
    const unitId = opt.data('id') || '';

    // Obtener información del producto seleccionado
    const productId = $('#edit_productid').val();
    let baseAdd = 0;
    let factor = 1;

    if (productId && unitCode) {
      // Obtener información del producto para calcular conversión correcta
      $.getJSON('/sale/getproductbyid/' + productId, function(resp) {
        if (resp && resp.success && resp.data.product) {
          const product = resp.data.product;

          if (product.sale_type === 'weight' && product.weight_per_unit) {
            if (unitCode === '59') {
              // Si es unidad (saco), usar directamente como base
              factor = 1;
              baseAdd = qty;
            } else if (unitCode === '36') {
              // Si es libra, usar directamente
              factor = 1;
              baseAdd = qty;
            } else {
              // Para otras unidades, usar factor por defecto
              factor = parseFloat(opt.data('factor') || '1');
              baseAdd = qty * factor;
            }
          } else if (product.sale_type === 'volume' && product.volume_per_unit) {
            if (unitCode === '59') {
              // Si es unidad (galón), convertir a litros
              factor = product.volume_per_unit;
              baseAdd = qty * factor;
            } else if (unitCode === '23') {
              // Si es litro, usar directamente
              factor = 1;
              baseAdd = qty;
            } else {
              // Para otras unidades, usar factor por defecto
              factor = parseFloat(opt.data('factor') || '1');
              baseAdd = qty * factor;
            }
          } else {
            // Para productos por unidad, no hay conversión
            factor = 1;
            baseAdd = qty;
          }

          // Actualizar campos
          $('#edit_conversion-factor').val(factor);
          $('#edit_selected-unit-id').val(unitId);

          if (qty > 0) {
            let baseUnit = 'unidad';
            if (product.sale_type === 'volume') {
              baseUnit = 'litros';
            } else if (product.sale_type === 'weight') {
              baseUnit = unitCode === '59' ? 'sacos' : 'libras';
            }
            $('#edit-base-add-display').text(baseAdd.toFixed(4) + ' ' + baseUnit);
            $('#edit-inventory-conversion-info').show();
          } else {
            $('#edit-inventory-conversion-info').hide();
          }
        }
      });
    } else {
      // Fallback si no hay producto seleccionado
      factor = parseFloat(opt.data('factor') || '1');
      baseAdd = qty * factor;
      $('#edit_conversion-factor').val(factor);
      $('#edit_selected-unit-id').val(unitId);

      if (qty > 0) {
        $('#edit-base-add-display').text(baseAdd.toFixed(4));
        $('#edit-inventory-conversion-info').show();
      } else {
        $('#edit-inventory-conversion-info').hide();
      }
    }
  }
  // Función para cargar unidades en modal de editar
  function loadEditUnits(productId, savedUnitId = null){
    if(!productId) return;
    $.getJSON('/sale/getproductbyid/' + productId, function(resp){
      if(!(resp && resp.success)) return;
      // Determinar qué unidades mostrar según el tipo de producto
      let allowedUnits = WEIGHT_UNITS; // Por defecto
      if(resp.data.product && resp.data.product.sale_type) {
        switch(resp.data.product.sale_type) {
          case 'volume':
            allowedUnits = VOLUME_UNITS;
            break;
          case 'weight':
            allowedUnits = WEIGHT_UNITS;
            break;
          case 'unit':
            allowedUnits = UNIT_UNITS;
            break;
        }
      }
      const units = (resp.data.units || []).filter(u => allowedUnits.includes(u.unit_code));
      const sel = $('#edit_unit-select');
      sel.empty().append('<option value="">Seleccionar unidad...</option>');
      units.forEach(u=>{
        sel.append(`<option value="${u.unit_code}" data-id="${u.unit_id}" data-factor="${u.conversion_factor}">${pretty(u.unit_code,u.unit_name)}</option>`);
      });

      // Seleccionar la unidad guardada si existe
      if (savedUnitId) {
        // Primero buscar por unit_id exacto
        let savedUnit = units.find(u => u.unit_id == savedUnitId);

        // Si no se encuentra, buscar por el código de unidad correspondiente
        if (!savedUnit) {
          // Obtener el código de unidad correspondiente al ID guardado
          const unitCodes = {
            '2': '36',   // Libra
            '11': '23',  // Litro
            '28': '59',  // Unidad
            '34': '99'   // Otra
          };
          const unitCode = unitCodes[savedUnitId];
          if (unitCode) {
            savedUnit = units.find(u => u.unit_code === unitCode);
          }
        }

        if (savedUnit) {
          sel.val(savedUnit.unit_code).trigger('change');
          $('#edit_selected-unit-id').val(savedUnit.unit_id);
          $('#edit_conversion-factor').val(savedUnit.conversion_factor);
        } else {
          // Si no encuentra la unidad guardada, seleccionar la primera disponible
          if (units.length) {
            sel.val(units[0].unit_code).trigger('change');
          }
        }
      } else {
        // Si no hay unidad guardada, seleccionar la primera disponible
        if (units.length) {
          sel.val(units[0].unit_code).trigger('change');
        }
      }
    });
  }
  // Hacer las funciones disponibles globalmente
  window.loadEditUnits = loadEditUnits;
  window.updateEditBasePreview = updateEditBasePreview;
})();
