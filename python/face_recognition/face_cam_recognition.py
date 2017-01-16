import os
import cv2

FACE_CASCADE_PATH = './cascade/haarcascade_frontalface_default.xml'

def main():
	face_cascade = cv2.CascadeClassifier(FACE_CASCADE_PATH)

	video = cv2.VideoCapture(0)

	while True:
		ret, frame = video.read()
		gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

		faces = face_cascade.detectMultiScale(gray_frame,
											  scaleFactor = 1.1,
											  minNeighbors = 5,
											  minSize = (30, 30),
											  flags = cv2.cv.CV_HAAR_SCALE_IMAGE)

		print("{} faces found in video".format(len(faces)))

		for (x, y, w, h) in faces:
			cv2.rectangle(frame, (x, y), (x+w, y+h), (0, 255, 0), 2)

		cv2.imshow('Face Video', frame)

		if cv2.waitKey(1) & 0xFF == ord('q'):
			break

	video.release()
	cv2.destroyAllWindows()

if __name__ == "__main__":
	main()