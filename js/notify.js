function notify(text, loglevel) {
            alertify.set({ delay: 10000 });
            switch (loglevel) {
                case 1:
                    alertify.log(text);                    
                    break;
            
                case 2:
                    alertify.success(text);                    
                    break;

                case 3:
                    alertify.error(text);                    
                    break;
            }
        }