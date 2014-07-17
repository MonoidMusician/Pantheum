import math
from pprint import pprint
import sys

def gt(count):
    places = []
    if istwo(count) == False:
        count = int(neartwo(count))
    
    print count
    maximum = int(math.log(count) / math.log(2))
    
    # W
    # L
    # G
    # P
    # S
    # F
    # C
    

    for place in range(1, count*2):
        if place % 2 == 1:
            next = 0
            if (place+1)%4 == 0:
                next = place-1
            else:
                next = place+1
            places.append(['T' + str(place), 'W' + str(next), 'L' + str(next)])
        elif place % 4 == 0:
            game = 0
            
            for x in range(0, maximum+1):
                if ((place+(2**x)) % (2**(x+1))) == 0:
                    game = x
                    break
            print game
            if ((place+(2**game)) % (2**(game+2))) == 0:
                next = place - (2**game)
            else:
                next = place + (2**game)
            
            if game < maximum:
                if (game % 2) == 1:
                    lplayin = count - place
                    if (lplayin < 0):
                        lplayin += (count*2)
                else:
                    lplayin = (count*2) - place
                places.append(['W' + str(place), 'W' + str(int(place-(2**(game-1)))), 'W' + str(int(place+(2**(game-1)))), 'W' + str(next), 'P' + str(next)])
                places.append(['L' + str(place), 'G' + str(place), 'P' + str(lplayin), 'G' + str(next), 'end'])
                places.append(['G' + str(place), 'L' + str(int(place-(2**(game-1)))), 'L' + str(int(place+(2**(game-1)))), 'L' + str(place), 'end'])
                places.append(['P' + str(place), 'W' + str(int(place-(2**(game-1)))), 'W' + str(int(place+(2**(game-1)))), 'L' + str(lplayin), 'end'])
            else:
                places.append(['W' + str(place), 'W' + str(int(place-(2**(game-1)))), 'W' + str(int(place+(2**(game-1)))), 'C' + str(place), 'W' + str(place)])
                places.append(['L' + str(place), 'G' + str(place), 'P' + str(place), 'S' + str(place), 'end'])
                places.append(['G' + str(place), 'L' + str(int(place-(2**(game-1)))), 'L' + str(int(place+(2**(game-1)))), 'L' + str(place), 'end'])
                places.append(['P' + str(place), 'W' + str(int(place-(2**(game-1)))), 'W' + str(int(place+(2**(game-1)))), 'L' + str(place), 'end'])
                places.append(['S' + str(place), 'L' + str(place), 'S' + str(place), 'end'])
                places.append(['C' + str(place), 'W' + str(place), 'S' + str(place), 'end', 'end'])
        elif place % 2 == 0:
            if ((place+2) % (2**3)) == 0:
                next = place - 2
            else:
                next = place + 2
            places.append(['W' + str(place), 'T' + str(place-1), 'T' + str(place+1), 'W' + str(next), 'P' + str(next)])
            places.append(['L' + str(place), 'T' + str(place-1), 'T' + str(place+1), 'G' + str(next), 'end'])
        else:
            print place
            sys.exit(-1)
            

    pprint(places)
        
def istwo(x):
    val1 = math.log(x) / math.log(2)
    val2 = math.floor(math.log(x) / math.log(2))
    
    if val1 == val2:
        return True
    else:
        return False
        
def neartwo(x):
    return math.pow(2, math.ceil(math.log(x) / math.log(2)))
    
