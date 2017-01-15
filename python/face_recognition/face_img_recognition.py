import os
import sys
import argparse
import cv2

FACE_CASCADE_PATH = './cascade/haarcascade_frontalface_default.xml' 

def image_exists(image_path = ''):
	if os.path.isfile(image_path):
		return True

	return False

def main():
	parser = argparse.ArgumentParser()
	parser.add_argument('--i', default='', help='Image Path')
	args = vars( parser.parse_args() )

	image_path = args['i']
	if image_exists(image_path):
		face_cascade = cv2.CascadeClassifier(FACE_CASCADE_PATH)
		image = cv2.imread(image_path)
		gray_image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

		faces = face_cascade.detectMultiScale(gray_image,
											  scaleFactor = 1.1,
											  minNeighbors = 5,
											  minSize = (30, 30),
											  flags = cv2.cv.CV_HAAR_SCALE_IMAGE)

		print("{} faces found in the image".format(len(faces)))

		for (x, y, w, h) in faces:
			cv2.rectangle(image, (x, y), (x+w, y+h), (0, 255, 0), 2)

		cv2.imshow("Faces in Image", image)
		cv2.waitKey(0)
	else:
		print('Image file cannot be found')


if __name__ == '__main__':
	main()
