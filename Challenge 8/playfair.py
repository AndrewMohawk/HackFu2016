# stoled from :https://stackoverflow.com/questions/5428069/my-python-method-isnt-working-correctly-skipping-items-in-a-list-playfair-cip
def makePlayFair(key, alpha):
	letters = []
	for letter in key + alpha:
		if letter not in letters:
			letters.append(letter)

	box = []
	for line_number in range(8):
		box.append( letters[line_number * 8: (line_number+1) * 8])
	return box

	


def decodePlayFair():
	f = open('list.txt','r')
	lines = f.readlines()
	for word in lines: 
		secret = word.replace(" ","").strip()
		secret = secret.upper()
		alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" + secret.lower() + "abcdefghijklmnopqrstuvwxyz0123456789=?"
		crypted = "eFjdlwKgeFlscbApnQEsny3tnye0frxnlrQ5vliW3Yx=5Al?S1nT4obQHql?Ozl?KqeG5252"
		ciphersquare = makePlayFair(secret,alphabet)
		grid = "";
		for row in ciphersquare:
			
			if(len(row) > 0):
				#print row
				grid = grid + ''.join(row)
		#print "crypted:",crypted;
		#print grid
		
		twoPair = [crypted[i:i+2] for i in range(0, len(crypted), 2)];
		ans = "";
		for two in twoPair:
			decryptedtwo = decryptDigraph(grid,two,8)
			#if(decryptedtwo == "=x"):
				#decryptedtwo = "="
			ans = ans + decryptedtwo
			#print two,"=",decryptedtwo
			
		print secret,":"
					
		xPositions = [i for i, ltr in enumerate(ans) if ltr == 'x']
		b = bytearray(ans)
		numFound = 0;
		for xPos in xPositions:
			if(xPos > 0 and xPos < len(ans)):
				
				if(ans[xPos-1:xPos] == ans[xPos+1:xPos+2]):
					del(b[xPos-numFound])
					numFound=numFound+1
		ans = str(b);
		
		print ans
		#print ans.replace("x"," ");
		import base64
		try:
			
			b64 = base64.b64decode(ans)
			
			print base64.b64decode(ans)
			#print base64.b64decode(ans.replace("x","").replace("X",""))
		except:
			pass
		print "-------------------------------"
	f.close()	

# decrypts a digraph using the defined grid
def decryptDigraph(grid, input,gridLen):
	if len(input) != 2:
		raise PlayfairError('The digraph that is going to be encrypted must be exactly 2 characters long.')
	
	firstEncrypted = input[0]
	secondEncrypted = input[1]
	
	
	firstEncryptedPosition = grid.find(firstEncrypted)
	secondEncryptedPosition = grid.find(secondEncrypted)
	
	firstEncryptedCoordinates = (firstEncryptedPosition % gridLen, firstEncryptedPosition / gridLen)
	secondEncryptedCoordinates = (secondEncryptedPosition % gridLen, secondEncryptedPosition / gridLen)
	
	if firstEncryptedCoordinates[0] == secondEncryptedCoordinates[0]: # letters are in the same column
		firstLetter = grid[(((firstEncryptedCoordinates[1] - 1) % gridLen) * gridLen) + firstEncryptedCoordinates[0]]
		secondLetter = grid[(((secondEncryptedCoordinates[1] - 1) % gridLen) * gridLen) + secondEncryptedCoordinates[0]]
	elif firstEncryptedCoordinates[1] == secondEncryptedCoordinates[1]: # letters are in the same row
		firstLetter = grid[(firstEncryptedCoordinates[1] * gridLen) + ((firstEncryptedCoordinates[0] - 1) % gridLen)]
		secondLetter = grid[(secondEncryptedCoordinates[1] * gridLen) + ((secondEncryptedCoordinates[0] - 1) % gridLen)]
	else: # letters are not in the same row or column, i.e. they form a rectangle
		firstLetter = grid[(firstEncryptedCoordinates[1] * gridLen) + secondEncryptedCoordinates[0]]
		secondLetter = grid[(secondEncryptedCoordinates[1] * gridLen) + firstEncryptedCoordinates[0]]
		
	return firstLetter+secondLetter

#secret = "youcannotspellscorchedearthwithoutdeath"
#scorcher

decodePlayFair()

#print x

