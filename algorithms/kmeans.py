from datetime import date, datetime, timedelta
from random import randrange
import maths

class Kmeans:

    def __init__(self):
        self.lastUpdate = date(2000, 1, 1)
        self.centroids = []

    def train(self, data, centroid_count=3, iterations=100):
        data_with_centroids = []
        centroids = []

        if len(data) > 1000:
            sampled_data = [data.pop(randrange(0, len(data))) for _ in range(1000)]
            data.clear()
            data.extend(sampled_data)

        for _ in range(centroid_count):
            centroids.append([randrange(0, 1000)/1000 for i in range(len(data[0]))])

        for _ in range(iterations):
            data_with_centroids.clear()
            for point in data:
                current_centroid = centroids[0]
                for centroid in centroids:
                    if maths.euclidean_distance(point, current_centroid) > maths.euclidean_distance(point, centroid):
                        current_centroid = centroid
                data_with_centroids.append([point, current_centroid])

            for _, centroid in enumerate(centroids):
                count = 0
                points = []
                scaling_factor = 5 * len(data)

                for point in data_with_centroids:
                    if point[1] == centroid:
                        count += 1
                        points.append(point[0])

                for point in points:
                    for j in range(len(centroid)):
                        centroid[j] += round(maths.means([point[j], centroid[j]]) / scaling_factor)

        self.lastUpdate = datetime.now().date()
        self.centroids = centroids.copy()
        return
    
    def exec (self, data, centroid_count=3, iterations=100):
        
        if self.lastUpdate + timedelta(days=7) < datetime.now().date():
            self.train(data, centroid_count, iterations)

        clusters = []
        for _ in range(len(self.centroids)):
            clusters.append([])

        for point in data:
            index = 0
            min = maths.euclidean_distance(self.centroids[0], point)
            for i in range(len(self.centroids)):
                if min < maths.euclidean_distance(self.centroids[i], point):
                    index = i
                    min = maths.euclidean_distance(self.centroids[i], point)
            clusters[index].append(point)

        return clusters
