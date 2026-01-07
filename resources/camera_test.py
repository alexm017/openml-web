import cv2
cap = cv2.VideoCapture(0) #If it doesn't work, increment the number by 1 until the camera works and appears on the screen
ret, frame = cap.read()
print("Camera working:", ret)
cap.release()