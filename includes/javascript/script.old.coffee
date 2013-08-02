#class Products
#  products: []
#  canvas: null
#  weared: []
#  page: 1
#  countpages:0
#  imgs: []
#  incurcath: []
#  constructor: (c) ->
#    @canvas = c
#  loadProducts: (callback)->
#    @page = 1
#    c = @
#    $.get '/ajax?api=products&model=' + @canvas.curmodel + '&front=' + @canvas.curfront + '&add=' + @canvas.curcath, (data)->
#      c.incurcath = data
#      for dataitem in data
#        if !c.getProductById(dataitem.id)
#          c.products.push(dataitem)
#      countpages=Math.ceil data.length/9
#      for product in data
#        c.imgs.push '/img/cloths/'+product.photo if !c.imgs.have product.photo
#        c.imgs.push '/img/clothsback/'+product.photoback if !c.imgs.have product.photoback
#      if(c.imgs.length>0)
#        imageLoader.loadimages c.imgs, callback
#      else
#        callback() if callback isnt undefined
#      return
#    ,'json'
#  getProductById: (id)->
#    for product in @products
#      return product if parseInt(product.id) is parseInt(id)
#    return false
#  wearProduct: (id)->
#    if product = @getProductById(id)
#      id = parseInt id
#      if !@weared.have id
#        @removeLayer(product.layer, product.id)
#        @weared.push id
#      else
#        @weared.remove id
#    @canvas.wearedChanged()
#    @canvas.onwear()
#    return
#  changeActive: ->
#    $('#items .item').removeClass('active')
#    for item in @weared
#      $('#items .item.product'+item).addClass('active')
#  checkProducts: ->
#    cur = @
#    @loadProducts ->
#      for product in cur.weared
#        cur.weared.remove product if !cur.getProductById(product)
#
#
#    return
#  sortProducts: ->
#    c=@
#    @weared.sort (a,b)->
#      a = c.getProductById(a).layer
#      b = c.getProductById(b).layer
#      return -1 if a<b
#      return 1 if a>b
#      return 0
#    return
#  generateDom: ->
#    c = @
#    if @page == undefined || @page < 1
#      @curpage = 1
#    $('#items .item').remove();
#    start = (@page-1)*9
#    if(start<=@incurcath.length-1)
#      end = (@page-1)*9+8
#      end = @incurcath.length-1 if(end>@incurcath.length-1)
#      for i in [start..end]
#        cur =  @incurcath[i]
#        item = $('<div class="item product'+cur.id+' data-id='+cur.id+'"></div>').data('id', cur.id)
#        item.append($('<div class="img"></div>').append($("<img src='"+@getImage(cur.id)+"'></a>")))
#        item.append("<div class='name'>"+cur.name+"</div>")
#        item.append("<div class='price'>"+cur.price+" Руб.</div>")
#        colors = $("<div class='colors'>")
#        for color in cur.colors
#          colors.append($("<div class='color'>").css('background-color', color))
#        $('#items').append(item.append(colors))
#    @changeActive()
#  removeLayer: (layer, id)->
#    for item in @weared
#      @weared.remove(item) if @getProductById(item).layer is layer  && @getProductById(item).id isnt id
#  drawCanvas: ->
#    @checkProducts()
#    @sortProducts()
#    itms = []
#    for itm in @weared
#      itms.push(@getProductById(itm)) if @getProductById(itm)
#    for item in itms
#      if item.models.indexOf(@canvas.curmodel+"") != -1
#        img = @getImage(item.id)
#        mdl = @canvas.getModelById(@canvas.curmodel)
#        model = if @canvas.curfront is 'f' then mdl.img else mdl.imgb
#        model = imageLoader.imgs['/img/models/' + model]
#        ads = item.ads[@canvas.curmodel]
#        adsx = parseInt(ads[@canvas.curfront+'x'])
#        adsy = parseInt(ads[@canvas.curfront+'y'])
#        adsw = parseInt(ads[@canvas.curfront+'w'])
#        adsh = parseInt(ads[@canvas.curfront+'h'])
#        @canvas.drawImage(img,200+adsx, @canvas.jcanvas.height() - model.height + adsy, adsw, adsh)
#    return
#  getImage: (id)->
#    if prod = @getProductById id
#
#      return if @canvas.curfront == 'f' then '/img/cloths/'+prod.photo else '/img/clothsback/'+prod.photoback
#
#class Canvas
#  canvas: null
#  context: null
#  models: []
#  curmodel: null
#  curcath: null
#  curfront: 'f'
#  products: null
#  oncathchanged: ->
#    console.log 'cath changed'
#    cur = @
#    @products.loadProducts ->
#      cur.onproductschanged()
#  onnextpage : ->
#    console.log 'next page'
#    @products.page++ if(@products.page < @products.countpages)
#    @products.generateDom()
#    @products.changeActive()
#  onprevpage : ->
#    console.log 'prev page'
#    @products.page-- if(@products.page > 1)
#    @products.generateDom()
#    @products.changeActive()
#  onmodelchanged: ->
#    console.log 'model changed'
#    @products.products= []
#    cur = @
#    @loadModels ->
#      console.log('models loaded')
#      cur.drawModel()
#      cur.products.loadProducts ->
#        cur.products.checkProducts()
#        cur.products.sortProducts()
#        cur.products.drawCanvas()
#        cur.products.changeActive()
#  onwearedchanged: ->
#    console.log 'weared changed'
#    @drawModel()
#    @products.checkProducts()
#    @products.sortProducts()
#    @products.drawCanvas()
#    @products.changeActive()
#  onproductschanged: ->
#    console.log 'products changed'
#    @products.checkProducts()
#    @products.generateDom()
#    @products.changeActive()
#  onfrontchanged: ->
#    console.log 'front changed'
#    @drawModel()
#    @products.drawCanvas()
#  onload: ->
#    console.log 'load'
#    @readHash()
#  onwear: ->
#    console.log 'wear'
#    @clear()
#    @drawModel()
#    @products.drawCanvas()
#    @products.changeActive()
#  imgs: []
#  clear: ->
#    @context.clearRect(0, 0, @jcanvas.width(), @jcanvas.height())
#  init: ->
#    @jcanvas = $('#wear')
#    @canvas = @jcanvas.get(0);
#    @context = @canvas.getContext('2d');
#    @context.fillStyle = 'black'
#    @context.font = 'bold 12px Arial'
#    @products = new Products(@)
#    @onload()
#    return
#  readHash: ->
#    # Разбили на части
#    params = window.location.hash.substr(1).split('-');
#    cur = @
#    #Работа с продуктами
#    nowproducts = @products.weared
#    if(params[3] isnt undefined )
#      forweared = params[3].split('/')
#      @products.weared = []
#      for wear in forweared
#        if(wear.isInt())
#
#          @products.weared.push(wear.getInt()) if !@products.weared.have(wear.getInt())
#    @onwearedchanged() if !nowproducts.same(@products.weared)
#    #Работа с моделью
#    nowmodel = @curmodel
#
#    @curmodel = params[0].getInt() if(params[0].isInt())
#    if @getModelById(@curmodel) == false
#      if(@models.length==0)
#        @loadModels ->
#          cur.curmodel = if cur.models.length>0 then cur.models[0].id else null
#          cur.onmodelchanged() if nowmodel isnt cur.urmodel
#      else
#        @curmodel = @models[0].id
#        @onmodelchanged() if nowmodel isnt @curmodell
#    #Работа со стороной
#    nowfront = @curfront
#    @curfront = params[1] if(params[1] is 'f' || params[1] is 'b')
#    @onfrontchanged() if nowfront isnt @curfront
#
#    #Работа с категорией
#    nowcath = @curcath
#    @curcath = params[2] if(params[2] != undefined)
#    @oncathchanged() if @curcath isnt nowcath
#
#
#  wearedChanged: ->
#    params = window.location.hash.substr(1).split('-');
#    params[3] = @products.weared.join('/')
#    window.location.hash = params.join('-')
#    return
#  changeModel: (id)->
#    if(@getModelById)
#      params = window.location.hash.substr(1).split('-');
#      params[0] = id
#      window.location.hash = params.join('-')
#    return
#
#  changeFront: ->
#    params = window.location.hash.substr(1).split('-');
#    front = 'f'
#    front = params[1] if(params[1] is 'f' || params[1] is 'b')
#    params[1] = if front is 'f' then 'b' else 'f'
#    window.location.hash = params.join('-')
#    return
#  setCath: (cath)->
#    params = window.location.hash.substr(1).split('-');
#    params[2] = cath
#    window.location.hash = params.join('-')
#    return
#  writeHash: ->
#    hash = [@curmodel, @curfront, @curcath, @products.weared.join('/')]
#    window.location.hash = hash.join('-');
#    @readHash()
#  loadModels: (callback)->
#    obj = @;
#    $.get '/ajax?api=models',
#    (data)->
#      imgs = []
#      obj.models = data;
#      for model in obj.models
#        imgs.push('/img/models/' + model.img);
#        imgs.push('/img/models/' + model.imgb);
#      if imgs.length>0
#        imageLoader.loadimages(imgs, callback)
#      else
#        callback() if callback != undefined
#      return
#    , 'json'
#    return
#  drawImage: (img, posx, posy, w, h)->
#    img = imageLoader.imgs[img]
#    @context.drawImage img, 0, 0, img.width, img.height, posx, posy, w, h
#  getModelById: (id)->
#    for model in @models
#      return model if model.id is parseInt(id)
#    return false;
#  drawModel: ->
#    @clear()
#
#    if @curmodel == null
#      if @models.length > 0
#        @curmodel = @models[0].id
#    if @curmodel == null || @getModelById(@curmodel) == false
#     return
#    mdl = @getModelById(@curmodel)
#    img = if @curfront is 'f' then mdl.img else mdl.imgb
#    model = imageLoader.imgs['/img/models/' + img]
#    @drawImage('/img/models/' + img, 200, @jcanvas.height() - model.height, model.width, model.height);
#  blockScreen: ->
#    $('body').append('<div class="blocker"><div class="preloader"><img src="/img/preloader.gif"></div></div>')
#  unblockScreen: ->
#    $('.blocker').remove();
#  rotate: ->
#    @changeFront()
#
#
#
#c = new Canvas
#$(->
#  c.init();
#  $('.cath>a, .subcath>a').click (e)->
#    e.preventDefault()
#    c.setCath $(@).attr('href')
#  $('.rotate').click (e)->
#    e.preventDefault()
#    c.rotate()
#  $(document).on 'click', '.item', ->
#    c.products.wearProduct($(this).data 'id')
#  $('.modelchange').click (e)->
#    e.preventDefault()
#    id = $(this).attr('href').split('-')[1]
#    c.changeModel(parseInt(id))
#  window.onhashchange = ->
#    c.readHash()
#)
#Array.prototype.addAll = (array)->
#   for item in array
#     this.push(item)
#Array.prototype.remove = ->
#  a = arguments
#  L = a.length;
#  while (L && this.length)
#    what = a[--L]
#    while ((ax = this.indexOf(what)) != -1)
#      this.splice(ax, 1)
#  @
#Array.prototype.indexOf = (element)->
#  for item in this
#    return
#Array.prototype.have = (element)->
#  return  if this.indexOf(element)==-1 then false else true
#Array.prototype.same = (array)->
#  return false if array.length isnt this.length
#  for item in array
#    return false if !this.have(item)
#  return true
#String.prototype.isInt = ->
#  int = true
#  ints = ["0","1","2","3","4","5","6","7","8","9"]
#  if this.length is 0
#    return false
#  for char in this
#    if !ints.have char
#      int = false
#  int
#String.prototype.getInt = ->
#  d = 0;
#  res = 0;
#  ints = ["0","1","2","3","4","5","6","7","8","9"]
#  for i in [this.length-1..0] by -1
#    if ints.have this[i]
#      res += Math.pow(10, d)*parseInt(this[i])
#      d+=1
#  res
