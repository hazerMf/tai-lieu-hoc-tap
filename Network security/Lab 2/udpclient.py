import socket

# Address and host
addr = '127.0.0.1'
port = 5000

# Create the client socket and connect
client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client_socket.connect((addr, port))

# Send the message "Hello" to the server
# ------ B22DCVT214 ------
print("Connected, sending message: ")

while(True):
    message = input()
    client_socket.send(message.encode())

    # Received the message from the server and print it to the terminal
    # ------ B22DCVT214 ------
    data = client_socket.recv(4096)
    print(data.decode())

# Close the connection
client_socket.close()
