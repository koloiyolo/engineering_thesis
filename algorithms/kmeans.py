from random import randrange

import maths


def kmeans(data, centroid_count, iters=100):

    min = maths.getmin(data)
    max = maths.getmax(data)

    data_with_centroids = []
    centroids = []
    clusters = []
    for _ in range(centroid_count):
        centroids.append([randrange(min[i], max[i]) for i in range(len(min))])

    for _ in range(iters):
        data_with_centroids.clear()
        for point in data:
            current_centroid = centroids[0]
            for centroid in centroids:
                if maths.euclidean_distance(point, current_centroid) > maths.euclidean_distance(point, centroid):
                    current_centroid = centroid
            data_with_centroids.append([point, current_centroid])

        for i, centroid in enumerate(centroids):
            count = 0
            points = []
            scaling_factor = 2 * len(data)

            for point in data_with_centroids:
                if point[1] == centroid:
                    count += 1
                    points.append(point[0])

            for point in points:
                for j in range(len(centroid)):
                    centroid[j] += round(maths.means([point[j], centroid[j]]) / scaling_factor)

    data_with_centroids.clear()
    for point in data:
        current_centroid = centroids[0]
        for centroid in centroids:
            if maths.euclidean_distance(point, current_centroid) > maths.euclidean_distance(point, centroid):
                current_centroid = centroid
        data_with_centroids.append([point, current_centroid])

    for centroid in centroids:
        cluster = []
        for point in data_with_centroids:
            if centroid == point[1]:
                cluster.append(point[0])
        clusters.append(cluster)

    return clusters
