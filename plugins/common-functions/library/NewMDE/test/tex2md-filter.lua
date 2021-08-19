return {
  {
    Math = function (meta)
	meta.text=string.gsub(meta.text,"%$","hhhh")
	meta.tag="equation"
        	return meta
    end,
  }
}

