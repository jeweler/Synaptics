Array.prototype.addAll = (array)->
  for item in array
    this.push(item)

Array.prototype.remove = ->
  a = arguments
  L = a.length;
  while (L && this.length)
    what = a[--L]
    while ((ax = this.indexOf(what)) != -1)
      this.splice(ax, 1)
  @
Array.prototype.have = (element)->
  for el in this
    return true if el is element
  return false
Array.prototype.same = (array)->
  return false if array.length isnt this.length
  for item in array
    return false if !this.have(item)
  return true
String.prototype.isInt = ->
  int = true
  ints = ["0","1","2","3","4","5","6","7","8","9"]
  if this.length is 0
    return false
  for char in this
    if !ints.have char
      int = false
  int
String.prototype.getInt = ->
  d = 0;
  res = 0;
  ints = ["0","1","2","3","4","5","6","7","8","9"]
  for i in [this.length-1..0] by -1
    if ints.have this[i]
      res += Math.pow(10, d)*parseInt(this[i])
      d+=1
  res
String.prototype.cmp = (el)->
  res = ''
  for i in [0..el.length-1]
    res+=this[i]
    return false if el[i] isnt this[i]
  return true
String.prototype.cmpget = (el)->
  res = ''
  for i in [0..el.length-1]
    res+=this[i]
    return false if el[i] isnt this[i]
  return res
