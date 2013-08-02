class Canvas
  jcanvas : null
  canvas : null
  context : null
  products : null
  models : null
  hash: null
  onload : ->
    @update()
  onloadmodels: ->
  onloadproducts: ->
  ondrawimage: ->
  onchangecath: ->
  onhashchange: ->
    @draw()
  init: ->
    cur = @
    @jcanvas = $('#wear')
    @canvas = @jcanvas.get(0)
    @context = @canvas.getContext('2d')
    @context.fillStyle = 'black'
    @context.font = 'bold 12px Arial'
    @models = new Models(@)
    @products = new Products(@)
    @hash = new Hash(@)
    @models.loadModels ->
      console.log('models loaded')
      cur.hash.readHash()
      cur.products.loadStart ->

        console.log('products start loaded')
        cur.products.loadProducts ->
          console.log('products loaded')
          cur.onload()
          return
        return
    return
  update: ->
    @clear()
    @models.drawModel()
    @products.drawWeared()
    @products.generateDom()
  draw: ->
    @clear()
    @models.drawModel()
    @products.drawWeared()
    @products.changeActive()
  drawImage: (img, x,y,w,h)->
    if(imageLoader.imgs[img] != undefined)
      @context.drawImage(imageLoader.imgs[img], 0,0,imageLoader.imgs[img].width,imageLoader.imgs[img].height, x,y,w,h)
  clear: ->
    @context.clearRect 0,0, @jcanvas.width(), @jcanvas.height()
  setPrice: (price)->
    @context.fillText("Цена: "+price+" Руб.", 100, 100);





class Hash
  canvas : null
  constructor: (canvas)->
    @canvas = canvas
  readHash: ->
    hash = location.hash.substr(1).split('-')
    @canvas.models.current = hash[0].getInt() if hash[0].isInt()
    @canvas.models.front = hash[1] if hash[1] isnt undefined && ['f','b'].have(hash[1])
    if hash[2] isnt undefined
      if hash[2].cmp('cath')
        @canvas.products.cath = 'cath'+hash[2].getInt()
      else if hash[2].cmp('subcath')
        @canvas.products.cath = 'subcath'+hash[2].getInt()
      else @canvas.products.cath = null
    if hash[3] isnt undefined
      this.canvas.products.weared = []
      products = hash[3].split('/')
      for product in products
        if product.isInt()
          this.canvas.products.weared.push(product.getInt()) if !@canvas.products.weared.have product.getInt()
  changeModel: (id)->
    @canvas.products.products = []
    @canvas.products.ids = []
    cur = @
    hash = location.hash.substr(1).split('-')
    hash[0] = id
    @canvas.models.current = id
    window.location.hash = '#'+hash.join('-')
    @canvas.products.loadProducts ->
      cur.canvas.products.generateDom()
      cur.canvas.update()
  changeCath: (cath)->
    cur = @
    hash = location.hash.substr(1).split('-')
    hash[2] = cath
    window.location.hash = '#'+hash.join('-')
    console.log(cath)
    @canvas.products.cath = cath
    @canvas.products.loadProducts ->
      cur.canvas.products.generateDom()
  changeFront: ->
    hash = location.hash.substr(1).split('-')
    hash[1] = if(@canvas.models.front is 'f') then 'b' else 'f'
    @canvas.models.front = hash[1]
    window.location.hash = '#'+hash.join('-')
    @canvas.update()
  changeWeared: ->
    hash = location.hash.substr(1).split('-')
    hash[3] = @canvas.products.weared.join('/')
    window.location.hash = '#'+hash.join('-')




class Products
  cath: null
  products: []
  incath: []
  ids: []
  weared: []
  page: 1
  constructor: (canvas)->
    @canvas = canvas
  loadStart: (callback)->
    cur = @
    imgs = []
    link = '/ajax?api=products&items='+JSON.stringify(@canvas.products.weared)+"&model="+@canvas.models.current
    $.get link, (data)->
      cur.incath = []
      for item in data
        imgs.push '/img/cloths/'+item.photo
        imgs.push '/img/clothsback/'+item.photoback
        cur.products[item.id] = item if !cur.ids.have(item.id)
        cur.ids.push(item.id)
      if(imgs.length>0)
        imageLoader.loadimages imgs, ->
          callback()
          return
        return
      else
        callback() if callback isnt undefined
        return
    ,'json'
  loadProducts: (callback)->
    cur = @
    imgs = []
    link = '/ajax?api=products&model='+@canvas.models.current
    link+= '&add='+@cath if @cath isnt null

    $.get link, (data)->
      cur.incath = []
      $('#items .count').text('Подходящие товары: '+data.length)
      for item in data
        imgs.push '/img/cloths/'+item.photo
        imgs.push '/img/clothsback/'+item.photoback
        cur.incath.push(item)
        cur.products[item.id] = item if !cur.ids.have(item.id)
        cur.ids.push(item.id)
      if(imgs.length>0)
        imageLoader.loadimages imgs, ->
          callback()
          return
        return
      else
        callback() if callback isnt undefined
        return
    ,'json'
  removeLayer: (layer, id)->
    for item in @weared
      @weared.remove(item) if @products[item].layer is layer && id isnt @products[item].id
  sortProducts: ->
    cur = @
    @weared.sort (a,b)->
      return -1 if cur.products[a].layer<cur.products[b].layer
      return 1 if cur.products[a].layer>cur.products[b].layer
      return 0

  wearProduct: (id)->
    if(@ids.have(id))
      if(@weared.have(id))
        @weared.remove(id)
      else
        @removeLayer(@products[id].layer, id)
        @weared.push(id)
        @sortProducts()
      @canvas.hash.changeWeared()
      @drawWeared()
    else
      @weared.remove(id)
  checkProducts: ->
    cur = @weared
    for item in @weared
      if !@ids.have(item)
        @weared.remove(item)
    if(!@weared.same(cur))
      @canvas.hash.changeWeared()
  drawProduct: (id)->
    console.log 'drawing product...'
    id = parseInt(id)
    if(@ids.have(id))
      model = @canvas.models.getImage()
      product = @products[id]
      img = if @canvas.models.front is 'f' then '/img/cloths/'+product.photo else '/img/clothsback/'+product.photoback
      ads = product.ads[@canvas.models.current]
      x = parseInt(ads[@canvas.models.front+'x'])
      y = parseInt(ads[@canvas.models.front+'y'])
      w = parseInt(ads[@canvas.models.front+'w'])
      h = parseInt(ads[@canvas.models.front+'h'])
      @canvas.drawImage(img,200+x, @canvas.jcanvas.height() - model.height + y, w, h)
    else
      return 'we dont have this product'
  drawWeared: ->
    @checkProducts()
    price = 0
    for item in @weared
      price += @products[item].price
      @drawProduct(item)
    @canvas.setPrice(price)
  generateDom: ->
    c = @
    if @page == undefined || @page < 1
      @curpage = 1
    $('#items .item').remove();
    start = (@page-1)*9
    if(start<=@incath.length-1)
      end = (@page-1)*9+8
      end = @incath.length-1 if(end>@incath.length-1)
      for i in [start..end]
        cur =  @incath[i]
        img = if @canvas.models.front is 'f' then '/img/cloths/'+cur.photo else '/img/clothsback/'+cur.photoback
        item = $('<div class="item product'+cur.id+'" data-id='+cur.id+'"></div>').data('id', cur.id)
        item.append($('<div class="img"></div>').append("<img src='"+img+"'>"))
        item.append("<div class='name'>"+cur.name+"</div>")
        item.append("<div class='price'>"+cur.price+" Руб.</div>")
        colors = $("<div class='colors'>")
        for color in cur.colors
          colors.append($("<div class='color'>").css('background-color', color))
        $('#items').append(item.append(colors))
        c.changeActive()
    @changeActive()
  changeActive:->
    $('#items .item').removeClass('active')
    for item in @weared
      $('#items .item.product'+item).addClass('active')





class Models
  models : []
  ids : []
  loaded : 0
  canvas : null
  front : 'f'
  current : 0
  imgs : []
  constructor: (canvas)->
    @canvas = canvas
  loadModels: (callback)->
    cur = @

    $.get '/ajax?api=models', (data)->
      for model in data
        cur.ids.push model.id
        cur.models[model.id] = model
        cur.imgs.push('/img/models/'+model.img) if !cur.imgs.have('/img/models/'+model.img)
        cur.imgs.push('/img/models/'+model.imgb) if !cur.imgs.have('/img/models/'+model.imgb)
      imageLoader.loadimages cur.imgs, ->
        cur.current = parseInt(cur.models[cur.ids[0]].id)
        cur.loaded = 1
        callback() if callback isnt undefined
      return
    , 'json'
  getImage: ->
    if(@ids.have(@current))
      cur = @models[@current]
      cur = if @front == 'f' then cur.img else cur.imgb
      return imageLoader.imgs['/img/models/'+cur]
    return false
  drawModel: ->
    console.log 'drawing model...'
    id = parseInt(@current)
    if(@ids.have(id))
      model = @canvas.models.getImage()
      cur = @models[@current]
      cur = if @front == 'f' then cur.img else cur.imgb
      src = '/img/models/'+cur
      @canvas.drawImage(src,200, @canvas.jcanvas.height() - model.height , model.width, model.height)
    else
      return 'we dont have this model'
c = new Canvas
$ ->
  c.init()
  $('.rotate a').click (e)->
    e.preventDefault()
    c.hash.changeFront()
  $('.modelchange').click (e)->
    e.preventDefault()
    id = $(this).attr('href').split('-')[1]
    c.hash.changeModel(id.getInt())
  $('.navigation .cath>a,.navigation .subcath>a').click (e)->
    e.preventDefault()
    c.hash.changeCath $(this).attr('href')
  $(document).on 'click', '.item', ->
    c.products.wearProduct($(this).data('id'))
  window.onhashchange = ->
    c.onhashchange()
