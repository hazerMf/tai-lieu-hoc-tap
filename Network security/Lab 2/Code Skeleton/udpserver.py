import socket

# Address and host
addr = '127.0.0.1'
port = 5000

# Create the server socket
server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind((addr, port))

# listen and connect to the client
# ------ B22DCVT214 ------
server_socket.listen()
print("Waiting...")
client, addr = server_socket.accept()
print("Connected from",addr)

while(True):
    # Received the message from the client and print it to the terminal
    # ------ B22DCVT214 ------
    data = client.recv(4096)
    print("Message from client: ",data.decode())

    # Send the message "ACK" to the clinet
    # ------ B22DCVT214 ------
    message = "ACK"
    client.send(message.encode())


# Close the connection
server_socket.close()
