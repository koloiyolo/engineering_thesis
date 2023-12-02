from random import randrange
import maths

def ahc(data, clusters_count):

    # create dedrogram
    dendrogram = [];
    for i in range(len(data) - 1):
        dendrogram.append([])
        for j in range(i + 1, len(data)):
            dendrogram[i].append(maths.euclidean_distance(data[i], data[j]))
    
    # init clusters
    clusters = []
    for i in range(len(data)):
        clusters.append([])
        clusters[i].append(data[i].copy())

    # limit iterations to cluster count
    while len(clusters) > clusters_count:

        # find minimum distance
        min = [dendrogram[0][1], 0, 1]
        for i in range(len(dendrogram)):
            for j in range(len(dendrogram[i])):
                if dendrogram[i][j] < min[0]:
                    min = [dendrogram[i][j], i, j]

        # append data point with min distance to target cluster
        for i in range(len(clusters[min[2]])):
            clusters[min[1]].append(clusters[min[2]][i])
        del clusters[min[2]]

        # del irrelevant info about that data point outside its current cluster
        del data[min[2]]
        offset = min[2] - 1
        for i in range(len(dendrogram)):
            if i >= offset:
                break
            del dendrogram[i][offset - i]
        del dendrogram[offset]

    
    return clusters